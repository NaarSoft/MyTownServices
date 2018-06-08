<?php
require 'phpmailer/PHPMailerAutoload.php';

$message   = 'Hello How are you?';

$mail = new PHPMailer(true);
$mail->Host = "smtp.office365.com";
$mail->Port       = 25;
$mail->SMTPSecure = '';
$mail->SMTPAuth   = false;
$mail->Username = "";   
$mail->Password = "";
$mail->SetFrom('montcalmcare-net.mail.protection.outlook.com', 'Prateek');
$mail->IsHTML(true);
$mail->MsgHTML($message);

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>