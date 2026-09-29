<?php 
class NewsletterController {
	function list() {
		$page_title = "Danh sách email";
		$newsletterRepository = new NewsletterRepository();
		$newsletters = $newsletterRepository->getAll();
		include "view/newsletter/list.php";
	}

	function sendEmail() {
		$page_title = "Gởi email";
		$newsletterRepository = new NewsletterRepository();
		$newsletters = $newsletterRepository->getAll();
		include "view/newsletter/sendEmail.php";
	}

	function send() {
		require_fields(['subject','description']);
		if (empty($_POST['emails']) || !is_array($_POST['emails'])) throw new InvalidArgumentException('Vui lòng chọn người nhận.');
		$mailSenderService = new MailSenderService();
		$from_email = SMTP_USERNAME;
		$from_name = 'Godashop';
		$subject = $_POST["subject"];
		$content = $_POST["description"];
		$to_name = "";
		foreach ($_POST["emails"] as $to_email) {
			if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email người nhận không hợp lệ.');
			if (!$mailSenderService->send($from_email, $from_name, $to_email, $to_name, $subject, $content)) throw new DomainException('Chưa gửi hết email. Vui lòng kiểm tra và thử lại.');
		}

		$_SESSION["message"] = "Đã gởi mail thành công";
		header("location: index.php?c=newsletter&a=sendEmail");
		exit;
		
	}

	function delete() {
		$email = input_text('email', $_GET);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Email không hợp lệ.');
		db_execute('DELETE FROM newsletter WHERE email = ?', [$email]);
		header('Location: index.php?c=newsletter&a=list');
		exit;
	}

	
}
