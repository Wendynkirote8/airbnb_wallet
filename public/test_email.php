<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your-email@gmail.com'; // Your Gmail address
    $mail->Password   = 'your-app-password';      // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'Your Name or Company');
    $mail->addAddress('recipient@example.com', 'Recipient Name');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = '<p>This is a test email sent via PHPMailer using Gmail SMTP.</p>';
    $mail->AltBody = 'This is a test email sent via PHPMailer using Gmail SMTP.';

    $mail->send();
    echo 'Email has been sent successfully.';
} catch (Exception $e) {
    echo "Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}";
}
?>
