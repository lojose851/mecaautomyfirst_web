<?php

require_once 'vendor/autoload.php';

$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.mailgun.org';
$mail->SMTPAuth = true;
$mail->Username = 'meca@sandboxb02bddbf930b4fd99c0eff5997765cd8.mailgun.org';
$mail->Password = 'pascual';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('meca@sandboxb02bddbf930b4fd99c0eff5997765cd8.mailgun.org', 'Contact form App');
$mail->addAddress('mecaautoupholstery@gmail.com', 'Mario Cruz');
$mail->isHTML(true);

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$inputs = $_POST;

$mail->Subject = $inputs['subject'] . ' - ' . $inputs['name'] . ' - ' . $inputs['email'];
$mail->Body  = $inputs['message'] . '<p>' . $inputs ['phone']. '</p>';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    die();
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Procesar</title>
</head>
<body>
    <h1>We have recived your message . WE will answer sooner possible.</h1>
</body>
</html>
