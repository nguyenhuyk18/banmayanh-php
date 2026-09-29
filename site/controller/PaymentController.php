<?php
class PaymentController {
    private function customer() {
        $customer = (new CustomerRepository())->findEmail($_SESSION['email'] ?? '');
        if (!$customer || !$customer->getIsActive()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để mua hàng.';
            header('Location: ' . app_url());
            exit;
        }
        return $customer;
    }
    function checkout() {
        $customer = $this->customer();
        $cart = (new CartStorage())->fetch();
        if (!$cart->items) {
            $_SESSION['error'] = 'Giỏ hàng rỗng.';
            header('Location: ' . app_url());
            exit;
        }
        $_SESSION['checkout_token'] = bin2hex(random_bytes(24));
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/payment/checkout.php';
    }
    function order() {
        global $conn;
        $customer = $this->customer();
        $token = input_text('checkout_token');
        if (!$token || !hash_equals($_SESSION['checkout_token'] ?? '', $token)) {
            throw new DomainException('Đơn hàng đã được gửi hoặc phiên thanh toán hết hạn. Vui lòng mở lại trang thanh toán.');
        }
        require_fields(['fullname','mobile','province','district','ward','address','payment_method']);
        if (!preg_match('/^0[0-9]{9}$/D', input_text('mobile'))) throw new InvalidArgumentException('Số điện thoại không hợp lệ.');
        $method = input_text('payment_method');
        if (!in_array($method, ['0','2'], true)) throw new InvalidArgumentException('Phương thức thanh toán không hợp lệ.');
        if ($method === '2' && !StripeService::configured()) throw new DomainException('Stripe chưa được cấu hình. Vui lòng chọn COD.');
        $province = validate_address(input_text('ward'), input_text('district'), input_text('province'));
        $quantities = $_SESSION['cart_items'] ?? [];
        if (!$quantities) throw new DomainException('Giỏ hàng rỗng.');
        $conn->begin_transaction();
        try {
            $cart = new Cart();
            ksort($quantities);
            foreach ($quantities as $id => $qty) {
                $id = positive_id($id);
                $stmt = $conn->prepare('SELECT id FROM product WHERE id = ? FOR UPDATE');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->get_result()->fetch_assoc();
                $cart->addProduct($id, $qty);
            }
            $orderId = (new OrderRepository())->save([
                'created_date'=>date('Y-m-d H:i:s'), 'order_status_id'=>1, 'staff_id'=>null,
                'customer_id'=>$customer->getId(), 'shipping_fullname'=>input_text('fullname'),
                'shipping_mobile'=>input_text('mobile'), 'payment_method'=>input_text('payment_method'),
                'shipping_ward_id'=>input_text('ward'), 'shipping_housenumber_street'=>input_text('address'),
                'shipping_fee'=>$province->getShippingFee(), 'delivered_date'=>date('Y-m-d', strtotime('+3 days'))
            ]);
            if (!$orderId) throw new RuntimeException('Could not create order');
            foreach ($cart->items as $item) {
                $item['order_id'] = $orderId;
                if (!(new OrderItemRepository())->save($item)) throw new RuntimeException('Could not create order item');
                $stmt = $conn->prepare('UPDATE product SET inventory_qty = inventory_qty - ? WHERE id = ? AND inventory_qty >= ?');
                $stmt->bind_param('iii', $item['qty'], $item['product_id'], $item['qty']);
                $stmt->execute();
                if ($stmt->affected_rows !== 1) throw new DomainException('Sản phẩm không còn đủ số lượng.');
            }
            if ($method === '2') {
                db_execute('INSERT INTO stripe_payment (order_id,amount,currency,status) VALUES (?, ?, ?, ?)',
                    [$orderId, $cart->total_price + $province->getShippingFee(), 'vnd', 'creating']);
            }
            $conn->commit();
        } catch (Throwable $error) {
            $conn->rollback();
            throw $error;
        }
        if ($method === '2') {
            try {
                $session = StripeService::checkout($orderId, $cart->items, $province->getShippingFee(), $customer->getEmail());
                if (empty($session['id']) || empty($session['url'])) throw new RuntimeException('Stripe Checkout did not return a session');
                db_execute("UPDATE stripe_payment SET session_id=?,status=IF(status='creating','pending',status) WHERE order_id=?", [$session['id'], $orderId]);
            } catch (Throwable $error) {
                error_log('Stripe Checkout creation failed: ' . $error->getMessage());
                $conn->begin_transaction();
                try {
                    $payment = db_execute('SELECT status FROM stripe_payment WHERE order_id=? FOR UPDATE', [$orderId])->get_result()->fetch_assoc();
                    if ($payment && $payment['status'] === 'creating') {
                        db_execute("UPDATE stripe_payment SET status='failed' WHERE order_id=?", [$orderId]);
                        db_execute('UPDATE `order` SET order_status_id=6 WHERE id=?', [$orderId]);
                        db_execute('UPDATE product p JOIN order_item oi ON oi.product_id=p.id SET p.inventory_qty=p.inventory_qty+oi.qty WHERE oi.order_id=?', [$orderId]);
                    }
                    $conn->commit();
                } catch (Throwable $rollbackError) { $conn->rollback(); throw $rollbackError; }
                throw new DomainException('Không thể mở Stripe Checkout. Vui lòng thử lại hoặc chọn COD.');
            }
            unset($_SESSION['checkout_token']);
            (new CartStorage())->clear();
            header('Location: ' . $session['url'], true, 303);
            exit;
        }
        unset($_SESSION['checkout_token']);
        (new CartStorage())->clear();
        $_SESSION['success'] = 'Đã tạo đơn hàng #' . $orderId . ' thành công.';
        header('Location: ' . app_url('index.php?c=payment&a=complete&order_id=' . $orderId));
        exit;
    }

    function complete() {
        $customer = $this->customer();
        $orderId = positive_id($_GET['order_id'] ?? 0);
        $order = require_record((new OrderRepository())->find($orderId));
        if ($order->getCustomerId() != $customer->getId()) {
            http_response_code(404);
            exit('Không tìm thấy đơn hàng.');
        }
        $stripePayment = null;
        if ($order->getPaymentMethod() == 2) {
            $stripePayment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$orderId])->get_result()->fetch_assoc();
        }
        require ABSPATH_SITE . 'view/payment/complete.php';
    }

    function stripeReturn() {
        $customer = $this->customer();
        $sessionId = input_text('session_id', $_GET);
        $payment = db_execute('SELECT sp.order_id FROM stripe_payment sp JOIN `order` o ON o.id=sp.order_id WHERE sp.session_id=? AND o.customer_id=?', [$sessionId,$customer->getId()])->get_result()->fetch_assoc();
        require_record($payment);
        $session = StripeService::retrieve($sessionId);
        $status = StripeService::reconcile($session);
        $_SESSION[$status === 'paid' ? 'success' : 'error'] = $status === 'paid'
            ? 'Stripe đã xác nhận thanh toán cho đơn hàng #' . $payment['order_id'] . '.'
            : 'Thanh toán đang chờ xác nhận. Vui lòng xem lại đơn hàng sau.';
        header('Location: ' . app_url('index.php?c=payment&a=complete&order_id=' . $payment['order_id']));
        exit;
    }

    function stripeCancel() {
        $customer = $this->customer();
        $order = require_record((new OrderRepository())->find(positive_id($_GET['order_id'] ?? 0)));
        if ($order->getCustomerId() != $customer->getId()) { http_response_code(404); exit('Không tìm thấy đơn hàng.'); }
        $_SESSION['error'] = 'Bạn đã đóng trang thanh toán Stripe. Đơn hàng đang chờ thanh toán; bạn có thể thử lại từ trang đơn hàng.';
        header('Location: ' . app_url('index.php?c=customer&a=orderDetail&id=' . $order->getId()));
        exit;
    }

    function stripeResume() {
        $customer = $this->customer();
        $orderId = positive_id($_GET['order_id'] ?? 0);
        $payment = db_execute('SELECT sp.* FROM stripe_payment sp JOIN `order` o ON o.id=sp.order_id WHERE sp.order_id=? AND o.customer_id=?', [$orderId,$customer->getId()])->get_result()->fetch_assoc();
        require_record($payment);
        if ($payment['status'] === 'paid') { header('Location: ' . app_url('index.php?c=customer&a=orderDetail&id=' . $orderId)); exit; }
        if ($payment['status'] !== 'pending' || !$payment['session_id']) throw new DomainException('Phiên thanh toán không còn hiệu lực.');
        $session = StripeService::retrieve($payment['session_id']);
        if (($session['status'] ?? '') === 'expired') {
            StripeService::reconcile($session, true);
            throw new DomainException('Phiên Stripe đã hết hạn. Vui lòng đặt lại đơn hàng.');
        }
        if (($session['payment_status'] ?? '') === 'paid') {
            StripeService::reconcile($session);
            header('Location: ' . app_url('index.php?c=customer&a=orderDetail&id=' . $orderId));
            exit;
        }
        if (empty($session['url'])) throw new DomainException('Không thể mở lại trang thanh toán Stripe.');
        header('Location: ' . $session['url'], true, 303);
        exit;
    }

    function stripeWebhook() {
        require_post();
        try {
            $event = StripeService::verifyWebhook(file_get_contents('php://input'), $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
            $type = $event['type'];
            if (in_array($type, ['checkout.session.completed','checkout.session.async_payment_succeeded','checkout.session.expired','checkout.session.async_payment_failed'], true)) {
                $session = $event['data']['object'] ?? [];
                StripeService::reconcile($session, in_array($type, ['checkout.session.expired','checkout.session.async_payment_failed'], true));
            }
            http_response_code(200);
            echo 'ok';
        } catch (Throwable $error) {
            error_log('Stripe webhook rejected: ' . $error->getMessage());
            http_response_code(400);
            echo 'invalid webhook';
        }
    }
}
