<?php 
class ContactController {
    function form() {
        require ABSPATH_SITE . 'view/contact/enhanced.php';
    }

    // Gởi mail 
    function sendEmail() {
		require_fields(['fullname','email','mobile','content']);
		if (!filter_var(input_text('email'), FILTER_VALIDATE_EMAIL) || !preg_match('/^0[0-9]{9}$/D', input_text('mobile'))) throw new InvalidArgumentException('Email hoặc số điện thoại không hợp lệ.');
		$fullname = h(input_text('fullname'));
		$email = h(input_text('email'));
		$mobile = h(input_text('mobile'));
		$message = nl2br(h(input_text('content')));
        $web = get_domain() . get_base_path();

        $to = SHOP_OWNER;
        $subject = 'Lensora Camera - Liên hệ';
        $content = "Xin chào chủ cửa hàng, <br>
        Dưới đây là thông tin khách hàng liên hệ: <br>
        Tên: $fullname, <br>
        Email: $email, <br>
        SĐT: $mobile, <br>
        Nội dung: $message <br>
        ----------------------<br>
        Được gởi từ trang web: $web
        ";
        $emailService = new EmailService();
        if (!$emailService->send($to, $subject, $content)) throw new DomainException('Chưa gửi được email. Vui lòng thử lại sau.');
        echo 'Đã gửi email đến chủ shop thành công';
    }

    function subscribe() {
        $email = input_text('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
        $existing = db_execute('SELECT email FROM newsletter WHERE email = ?', [$email])->get_result()->fetch_assoc();
        if (!$existing) db_insert('newsletter', ['email'=>$email]);
        $_SESSION['success'] = 'Đã đăng ký nhận bản tin.';
        header('Location: ' . app_url());
        exit;
    }
}
