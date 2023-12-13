<?php

require '/Users/morristung/PHPMailer/src/Exception.php';
require '/Users/morristung/PHPMailer/src/PHPMailer.php';
require '/Users/morristung/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function send_email_verification($toEmail, $subject, $htmlBody) {

    
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'ssl://smtp.gmail.com';                 //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '';                   //SMTP username
        $mail->Password   = '';                  //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
        $mail->setFrom('dummorris@gmail.com', 'Mailer');
        $mail->addAddress($toEmail);     //Add a recipient
        
        
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody ="";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<script>conssole.log('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        return false;
    }
}
?>