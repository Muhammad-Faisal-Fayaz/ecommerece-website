<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'fa0325isalkhan@gmail.com';
    $mail->Password = 'ucqemfjkhvgsqyhd';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

    $mail->setFrom('fa0325isalkhan@gmail.com', 'ecommerece ');
    $mail->addAddress('fa0325isalkhan@gmail.com');

    $mail->Subject = "Test Email";
    $mail->Body = "Hello! PHPMailer is working perfectly.";

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}