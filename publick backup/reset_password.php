<?php
session_start();
require '../config/db_connect.php';

$error = "";
$success = "";
$token = isset($_GET["token"]) ? $_GET["token"] : '';

if (!$token) {
    die("Invalid password reset token.");
}

try {
    // Retrieve the token info and join with users table if needed
    $stmt = $pdo->prepare("SELECT pr.user_id, pr.expires FROM password_resets pr WHERE pr.token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset || strtotime($reset["expires"]) < time()) {
        die("This password reset link is invalid or has expired.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Update the user's password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$password_hash, $reset["user_id"]]);

            // Delete the token so it cannot be reused
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $success = "Your password has been reset successfully. You can now <a href='login.php'>login</a>.";
        }
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
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
      <h2 class="text-center">Reset Password</h2>
      <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
      <?php else: ?>
          <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
              <div class="mb-3">
                  <label for="new_password" class="form-label">New Password</label>
                  <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Enter new password" required>
              </div>
              <div class="mb-3">
                  <label for="confirm_password" class="form-label">Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm new password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Reset Password</button>
          </form>
      <?php endif; ?>
  </div>
</body>
</html>
