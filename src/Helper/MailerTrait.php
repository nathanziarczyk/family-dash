<?php


namespace App\Helper;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

trait MailerTrait
{
    public function sendMail($emailTo, $link)
    {
        //Create a new PHPMailer instance
        $mail = new PHPMailer;

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        //Set the hostname of the Mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $Mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $_ENV['EMAIL_ADDRESS'];

        //Password to use for SMTP authentication
        $mail->Password = $_ENV['EMAIL_PASS'];

        //Set who the message is to be sent from
        $mail->setFrom('teamfamilydash@gmail.com', 'Nathan Ziarczyk');

        //Set an alternative reply-to address
        $mail->addReplyTo('teamfamilydash@gmail.com', 'Nathan Ziarczyk');

        //Set who the message is to be sent to
        $mail->addAddress($emailTo);

        //Set the subject line
        $mail->Subject = 'Confirm FamilyDash email';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML(str_replace('%%link%%', $link, file_get_contents(__DIR__.'/../Mail/mail.html')), __DIR__);

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        //send the message, check for errors
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
    }
}