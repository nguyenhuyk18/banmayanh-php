<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController {
    private function customer() {
        $customer = (new CustomerRepository())->findEmail($_SESSION['email'] ?? '');
        if (!$customer || !$customer->getIsActive()) {
            unset($_SESSION['email']);
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            header('Location: ' . app_url());
            exit;
        }
        return $customer;
    }
    function show() {
        $customer = $this->customer();
        require ABSPATH_SITE . 'view/customer/show.php';
    }
    function shippingDefault() {
        $customer = $this->customer();
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/customer/shippingDefault.php';
    }
    function updateShippingDefault() {
        $customer = $this->customer();
        require_fields(['fullname','mobile','ward','district','province','address']);
        $this->validateContact();
        validate_address(input_text('ward'), input_text('district'), input_text('province'));
        $customer->setShippingName(input_text('fullname'));
        $customer->setShippingMobile(input_text('mobile'));
        $customer->setWardId(input_text('ward'));
        $customer->setHousenumberStreet(input_text('address'));
        (new CustomerRepository())->update($customer);
        $_SESSION['success'] = 'Đã cập nhật địa chỉ giao hàng.';
        header('Location: ' . app_url('index.php?c=customer&a=shippingDefault'));
        exit;
    }
    function orders() {
        $customer = $this->customer();
        $orders = (new OrderRepository())->getByCustomerId($customer->getId());
        $paymentsByOrder = [];
        $payments = db_execute('SELECT sp.order_id,sp.status FROM stripe_payment sp JOIN `order` o ON o.id=sp.order_id WHERE o.customer_id=?', [$customer->getId()])->get_result();
        while ($payment = $payments->fetch_assoc()) $paymentsByOrder[$payment['order_id']] = $payment['status'];
        require ABSPATH_SITE . 'view/customer/orders.php';
    }
    function orderDetail() {
        $customer = $this->customer();
        $order = require_record((new OrderRepository())->find(positive_id($_GET['id'] ?? 0)));
        if ($order->getCustomerId() != $customer->getId()) { http_response_code(404); exit('Không tìm thấy đơn hàng.'); }
        $stripePayment = db_execute('SELECT status FROM stripe_payment WHERE order_id=?', [$order->getId()])->get_result()->fetch_assoc();
        require ABSPATH_SITE . 'view/customer/orderDetail.php';
    }
    private function validateContact() {
        if (mb_strlen(input_text('fullname')) < 2 || mb_strlen(input_text('fullname')) > 100 || !preg_match('/^0[0-9]{9}$/D', input_text('mobile'))) {
            throw new InvalidArgumentException('Họ tên hoặc số điện thoại không hợp lệ.');
        }
    }
    function updateAccount() {
        $customer = $this->customer();
        $this->validateContact();
        $password = input_text('password');
        if ($password !== '' || input_text('current_password') !== '') {
            if (!password_verify(input_text('current_password'), $customer->getPassword())) throw new InvalidArgumentException('Mật khẩu hiện tại không đúng.');
            validate_password($password, input_text('password_confirmation'));
            $customer->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }
        $customer->setName(input_text('fullname'));
        $customer->setMobile(input_text('mobile'));
        (new CustomerRepository())->update($customer);
        $_SESSION['name'] = $customer->getName();
        $_SESSION['success'] = 'Đã cập nhật thông tin tài khoản.';
        header('Location: ' . app_url('index.php?c=customer&a=show'));
        exit;
    }
    function notExistingEmail() {
        $email = input_text('email', $_GET);
        json_response((bool) filter_var($email, FILTER_VALIDATE_EMAIL) && !(new CustomerRepository())->findEmail($email));
    }
    private function token($customer, $purpose) {
        return JWT::encode(['email'=>$customer->getEmail(), 'purpose'=>$purpose, 'iat'=>time(), 'exp'=>time()+3600, 'version'=>hash('sha256', $customer->getPassword())], JWT_KEY, 'HS256');
    }
    private function tokenCustomer($token, $purpose) {
        try { $claims = JWT::decode($token, new Key(JWT_KEY, 'HS256')); }
        catch (Throwable $error) { throw new InvalidArgumentException('Liên kết không hợp lệ hoặc đã hết hạn.'); }
        $customer = (new CustomerRepository())->findEmail($claims->email ?? '');
        if (!$customer || ($claims->purpose ?? '') !== $purpose || !hash_equals(hash('sha256', $customer->getPassword()), $claims->version ?? '')) throw new InvalidArgumentException('Liên kết không hợp lệ hoặc đã được sử dụng.');
        return $customer;
    }
    function register() {
        require_fields(['fullname','mobile','email','password','password_confirmation']);
        $this->validateContact();
        validate_password(input_text('password'), input_text('password_confirmation'));
        $email = input_text('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
        $repository = new CustomerRepository();
        if ($repository->findEmail($email)) throw new InvalidArgumentException('Email đã được đăng ký.');
        $id = $repository->save(['name'=>input_text('fullname'),'password'=>password_hash(input_text('password'), PASSWORD_DEFAULT),'mobile'=>input_text('mobile'),'email'=>$email,'login_by'=>'form','shipping_name'=>input_text('fullname'),'shipping_mobile'=>input_text('mobile'),'ward_id'=>null,'is_active'=>1,'housenumber_street'=>'']);
        if (!$id) throw new RuntimeException('Không thể tạo tài khoản. Vui lòng thử lại.');
        session_regenerate_id(true);
        $_SESSION['name'] = input_text('fullname');
        $_SESSION['email'] = $email;
        $_SESSION['success'] = 'Đăng ký thành công. Tài khoản của bạn đã sẵn sàng sử dụng.';
        header('Location: ' . app_url('index.php?c=customer&a=show'));
        exit;
    }
    function activeAccount() {
        $customer = $this->tokenCustomer(input_text('token', $_GET), 'activate');
        if ($customer->getIsActive()) throw new InvalidArgumentException('Tài khoản đã được kích hoạt.');
        $customer->setIsActive(1);
        (new CustomerRepository())->update($customer);
        $_SESSION['success'] = 'Đã kích hoạt tài khoản. Vui lòng đăng nhập.';
        header('Location: ' . app_url());
        exit;
    }
    function forgotPassword() {
        $email = input_text('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
        $customer = (new CustomerRepository())->findEmail($email);
        if ($customer) {
            $link = get_domain() . app_url('index.php?c=customer&a=resetPasword&token=' . rawurlencode($this->token($customer, 'reset')));
            if (!(new EmailService())->send($email, 'Lensora Camera - Đặt lại mật khẩu', '<p><a href="' . h($link) . '">Đặt lại mật khẩu</a>. Liên kết có hiệu lực trong 1 giờ.</p>')) throw new DomainException('Chưa gửi được email. Vui lòng thử lại sau.');
        }
        $_SESSION['success'] = 'Nếu email đã đăng ký, bạn sẽ nhận được liên kết đặt lại mật khẩu.';
        header('Location: ' . app_url());
        exit;
    }
    function resetPasword() {
        $token = input_text('token', $_GET);
        $this->tokenCustomer($token, 'reset');
        require ABSPATH_SITE . 'view/customer/resetPassword.php';
    }
    function updatePassword() {
        $customer = $this->tokenCustomer(input_text('token'), 'reset');
        validate_password(input_text('password'), input_text('password_confirmation'));
        $customer->setPassword(password_hash(input_text('password'), PASSWORD_DEFAULT));
        (new CustomerRepository())->update($customer);
        $_SESSION['success'] = 'Đã đổi mật khẩu. Vui lòng đăng nhập.';
        header('Location: ' . app_url());
        exit;
    }
}
