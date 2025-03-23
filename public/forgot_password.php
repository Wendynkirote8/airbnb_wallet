<?php
session_start();
require '../config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    try {
        // Check if the email exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate a secure token and set expiration (e.g., 1 hour from now)
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Insert token into password_resets table
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
            $stmt->execute([$user["user_id"], $token, $expires]);

            // Construct the reset link (adjust the domain/path as needed)
            $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $message = "Hello,\n\nPlease click the following link to reset your password:\n" . $reset_link . "\n\nIf you did not request a password reset, please ignore this email.";
            $headers = "From: no-reply@yourdomain.com";

            // Send email
            if (mail($email, $subject, $message, $headers)) {
                $success = "A password reset link has been sent to your email address.";
            } else {
                $error = "Failed to send email. Please try again.";
            }
        } else {
            // For security, show a generic message even if the email doesn't exist
            $success = "A password reset link has been sent to your email address.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body {
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
          background-color: #f8f9fa;
      }
      .container {
          background: white;
          padding: 30px;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }
  </style>
</head>
<body>
  <div class="container">
      <h2 class="text-center">Forgot Password</h2>
      <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>
      <form action="forgot_password.php" method="POST">
          <div class="mb-3">
              <label for="email" class="form-label">Enter your email address</label>
              <input type="email" name="email" class="form-control" id="email" placeholder="you@example.com" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
      </form>
      <div class="mt-3 text-center">
          <a href="login.php">Back to Login</a>
      </div>
  </div>
</body>
</html>
