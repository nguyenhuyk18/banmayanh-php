<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    function send($to, $subject, $content) {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";// fix lỗi tiếng việt
        try {
            //Server settings
            // Tắt debuf ở chỗ này sửa thành off
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            // Tắt các bước hiển thị gởi email
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = SMTP_USERNAME;                     //SMTP username
            $mail->Password   = SMTP_SECRET;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(SMTP_USERNAME);
            $mail->addAddress($to);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $content;

            $mail->send();
            return true;
            // echo 'Đã gửi mail thành công';

        } catch (Exception $e) {
            error_log('Email could not be sent: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
