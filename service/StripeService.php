<?php
class StripeService {
    static function secretKey() { return trim((string) (getenv('STRIPE_SECRET_KEY') ?: '')); }
    static function webhookSecret() { return trim((string) (getenv('STRIPE_WEBHOOK_SECRET') ?: '')); }
    static function configured() {
        $key = self::secretKey();
        if (!preg_match('/^sk_(test|live)_[A-Za-z0-9]+$/D', $key)) return false;
        if (strpos($key, 'sk_live_') === 0 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) return false;
        return true;
    }

    static function request($method, $path, array $params = []) {
        if (!self::configured()) throw new DomainException('Stripe chưa được cấu hình. Vui lòng dùng COD hoặc liên hệ quản trị viên.');
        $url = 'https://api.stripe.com/v1/' . ltrim($path, '/');
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>10, CURLOPT_TIMEOUT=>25,
            CURLOPT_USERPWD=>self::secretKey() . ':', CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_CUSTOMREQUEST=>$method, CURLOPT_HTTPHEADER=>['Content-Type: application/x-www-form-urlencoded']
        ]);
        if ($method === 'POST') curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params, '', '&', PHP_QUERY_RFC3986));
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($body === false) throw new RuntimeException('Stripe connection failed: ' . $error);
        $data = json_decode($body, true);
        if ($status < 200 || $status >= 300 || !is_array($data)) {
            error_log('Stripe API error: ' . substr($body, 0, 1000));
            if (($data['error']['code'] ?? '') === 'amount_too_small') {
                throw new DomainException('Tổng tiền chưa đạt mức tối thiểu của Stripe. Vui lòng thêm sản phẩm hoặc chọn COD.');
            }
            throw new DomainException('Không thể tạo giao dịch Stripe. Vui lòng thử lại hoặc chọn COD.');
        }
        return $data;
    }

    static function checkout($orderId, $items, $shippingFee, $customerEmail) {
        $lineItems = [];
        foreach (array_values($items) as $item) {
            $lineItems[] = [
                'price_data'=>['currency'=>'vnd','unit_amount'=>(int) $item['unit_price'],
                    'product_data'=>['name'=>mb_substr($item['name'], 0, 200)]],
                'quantity'=>(int) $item['qty']
            ];
        }
        if ($shippingFee > 0) {
            $lineItems[] = ['price_data'=>['currency'=>'vnd','unit_amount'=>(int) $shippingFee,
                'product_data'=>['name'=>'Phí giao hàng']], 'quantity'=>1];
        }
        return self::request('POST', 'checkout/sessions', [
            'mode'=>'payment', 'payment_method_types'=>['card'],
            'line_items'=>$lineItems, 'client_reference_id'=>(string) $orderId,
            'metadata'=>['order_id'=>(string) $orderId], 'customer_email'=>$customerEmail,
            'success_url'=>get_domain() . app_url('index.php?c=payment&a=stripeReturn&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'=>get_domain() . app_url('index.php?c=payment&a=stripeCancel&order_id=' . $orderId)
        ]);
    }

    static function retrieve($sessionId) {
        if (!preg_match('/^cs_(?:test|live)_[A-Za-z0-9]+$/D', $sessionId)) throw new InvalidArgumentException('Mã thanh toán không hợp lệ.');
        return self::request('GET', 'checkout/sessions/' . rawurlencode($sessionId));
    }

    static function verifyWebhook($body, $signature, $secret = null, $now = null) {
        $secret = $secret ?? self::webhookSecret();
        if (!$secret || strlen($body) > 1024 * 1024) throw new InvalidArgumentException('Webhook Stripe không hợp lệ.');
        $parts = [];
        foreach (explode(',', $signature) as $part) {
            $pair = explode('=', trim($part), 2);
            if (count($pair) === 2) $parts[$pair[0]][] = $pair[1];
        }
        $time = $parts['t'][0] ?? '';
        if (!ctype_digit($time) || abs(($now ?? time()) - (int) $time) > 300) throw new InvalidArgumentException('Webhook Stripe đã hết hạn.');
        $expected = hash_hmac('sha256', $time . '.' . $body, $secret);
        $valid = false;
        foreach ($parts['v1'] ?? [] as $candidate) if (hash_equals($expected, $candidate)) $valid = true;
        if (!$valid) throw new InvalidArgumentException('Chữ ký webhook Stripe không hợp lệ.');
        $event = json_decode($body, true);
        if (!is_array($event) || empty($event['id']) || empty($event['type'])) throw new InvalidArgumentException('Dữ liệu webhook Stripe không hợp lệ.');
        return $event;
    }

    static function reconcile(array $session, $failed = false) {
        global $conn;
        $orderId = positive_id($session['metadata']['order_id'] ?? $session['client_reference_id'] ?? 0);
        $sessionId = (string) ($session['id'] ?? '');
        $amount = (int) ($session['amount_total'] ?? -1);
        $currency = strtolower((string) ($session['currency'] ?? ''));
        $conn->begin_transaction();
        try {
            $payment = db_execute('SELECT * FROM stripe_payment WHERE order_id = ? FOR UPDATE', [$orderId])->get_result()->fetch_assoc();
            if (!$payment || $payment['session_id'] !== $sessionId || (int) $payment['amount'] !== $amount || $currency !== 'vnd') throw new DomainException('Giao dịch Stripe không khớp với đơn hàng.');
            if ($payment['status'] === 'paid') { $conn->commit(); return 'paid'; }
            if ($session['payment_status'] === 'paid') {
                if ($payment['status'] === 'expired' || $payment['status'] === 'failed') throw new DomainException('Đơn hàng đã được hủy; cần kiểm tra hoàn tiền.');
                db_execute("UPDATE stripe_payment SET status='paid' WHERE order_id=?", [$orderId]);
                $conn->commit();
                return 'paid';
            }
            if ($failed && in_array($payment['status'], ['creating','pending'], true)) {
                db_execute("UPDATE stripe_payment SET status='expired' WHERE order_id=?", [$orderId]);
                db_execute('UPDATE `order` SET order_status_id=6 WHERE id=?', [$orderId]);
                db_execute('UPDATE product p JOIN order_item oi ON oi.product_id=p.id SET p.inventory_qty=p.inventory_qty+oi.qty WHERE oi.order_id=?', [$orderId]);
                $conn->commit();
                return 'expired';
            }
            $conn->commit();
            return $payment['status'];
        } catch (Throwable $error) { $conn->rollback(); throw $error; }
    }
}
