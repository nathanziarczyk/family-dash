<?php


namespace App\Helper;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

trait MailerTrait
{
    public function sendMail($emailTo, $link)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_ADDRESS'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->setFrom('teamfamilydash@gmail.com', 'Nathan Ziarczyk');
        $mail->addReplyTo('teamfamilydash@gmail.com', 'Nathan Ziarczyk');
        $mail->addAddress($emailTo);
        $mail->Subject = 'Confirm FamilyDash email';
        $mail->msgHTML(str_replace('%%link%%', $link, file_get_contents(__DIR__.'/../Mail/mail.html')), __DIR__);
        $mail->AltBody = 'This is a plain-text message body';
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
    }
}


