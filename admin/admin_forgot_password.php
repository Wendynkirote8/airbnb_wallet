<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/db_connect.php';

// If using Composer, include the autoloader:
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use FILTER_SANITIZE_SPECIAL_CHARS instead of FILTER_SANITIZE_STRING
    $usernameOrEmail = trim(filter_input(INPUT_POST, 'usernameOrEmail', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($usernameOrEmail)) {
        $error = "Please enter your username or email.";
    } else {
        // Check if this admin exists
        try {
            $stmt = $pdo->prepare("SELECT admin_id, username, email FROM admins WHERE username = :ue OR email = :ue");
            $stmt->bindParam(':ue', $usernameOrEmail);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                // 1. Generate a unique token
                $token = bin2hex(random_bytes(16));
                $adminId = $admin['admin_id'];

                // 2. Store the token in a password_resets table (or in the admins table)
                $stmt = $pdo->prepare("
                    INSERT INTO admin_password_resets (admin_id, token, created_at) 
                    VALUES (:admin_id, :token, NOW())
                    ON DUPLICATE KEY UPDATE token = :token, created_at = NOW()
                ");
                $stmt->execute([':admin_id' => $adminId, ':token' => $token]);

                // 3. ***UPDATE HERE***: Send an email with the reset link using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Use your SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'your-email@gmail.com'; // Your real email address
                    $mail->Password   = 'your-app-password';      // Your email's app-specific password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    
                    // Recipients
                    $mail->setFrom('your-email@gmail.com', 'weshPAY Admin');
                    // Ensure $admin['email'] contains the admin's email address from your database
                    $mail->addAddress($admin['email'], $admin['username']);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    // Replace yourdomain.com with your actual domain
                    $resetLink = 'https://yourdomain.com/admin/admin_reset_password.php?token=' . urlencode($token);
                    $mail->Body    = "Hello " . htmlspecialchars($admin['username']) . ",<br><br>Please click the link below to reset your password:<br><a href='{$resetLink}'>Reset Password</a><br><br>If you did not request a password reset, please ignore this email.";
                    $mail->AltBody = "Hello " . $admin['username'] . ",\n\nPlease visit the following URL to reset your password:\n{$resetLink}\n\nIf you did not request a password reset, please ignore this email.";

                    $mail->send();
                    $success = "A reset link has been sent to your email.";
                } catch (Exception $e) {
                    $error = "Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                $error = "No admin found with that username or email.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 500px; margin-top: 50px;">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center">Forgot Password</h2>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="admin_forgot_password.php" method="POST">
                <div class="mb-3">
                    <label for="usernameOrEmail" class="form-label">Username or Email</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="usernameOrEmail" 
                      name="usernameOrEmail" 
                      placeholder="Enter your username or email" 
                      required
                    >
                </div>
                <button type="submit" class="btn btn-primary w-100">Request Reset</button>
            </form>

            <div class="mt-3 text-center">
                <a href="admin_login.php">Back to Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
