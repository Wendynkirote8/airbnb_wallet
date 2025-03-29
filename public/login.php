<?php
session_start();
require '../config/db_connect.php';

// Retrieve session messages, if any.
$loginError   = $_SESSION['error'] ?? "";
$loginSuccess = $_SESSION['success'] ?? "";
unset($_SESSION['error'], $_SESSION['success']);

$error = ""; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try {
        // Retrieve totp_secret along with other fields.
        $stmt = $pdo->prepare("SELECT user_id, full_name, password_hash, totp_secret FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {
            if (!empty($user["totp_secret"])) {
                // MFA is enabled – redirect to verification page.
                $_SESSION["pending_2fa_user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                header("Location: verify_2fa.php");
                exit();
            } else {
                // MFA is not enabled – force user to set it up.
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                header("Location: enable_2fa.php");
                exit();
            }
        } else {
            $error = "Invalid email or password.";
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
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login - weshPAY</title>
   <link rel="icon" href="path/to/favicon.ico" />
   <!-- Google Font: Poppins -->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="../assets/css/login_reg.css">
   <style>
      .google-login { margin-top: 15px; text-align: center; }
      .google-login a {
         display: inline-block; padding: 10px 20px;
         background: #4285F4; color: #fff;
         border-radius: 5px; text-decoration: none;
         font-family: 'Poppins', sans-serif;
      }
      .google-login a i { margin-right: 5px; }
      .alert { margin-bottom: 15px; padding: 10px; border-radius: 5px; }
      .alert-danger { background: #f8d7da; color: #721c24; }
      .alert-success { background: #d4edda; color: #155724; }
   </style>
</head>
<body>
  <!-- Header / Navigation -->
  <header>
    <div class="logo">weshPAY</div>
    <nav>
      <a href="landing.php">Home</a>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    </nav>
  </header>
  
  <!-- Main Content -->
  <main>
    <div class="landscape-container">
      <!-- Left Image Section -->
      <div class="image-section">
        <img src="../uploads/landing_page/login_landscape.jpg" alt="Login Landscape">
      </div>
      <!-- Right Form Section -->
      <div class="form-section">
        <h2>Login</h2>
        <!-- Display any session messages -->
        <?php if (!empty($loginError)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>
        <?php if (!empty($loginSuccess)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($loginSuccess); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
          </div><br>
          <button type="submit" class="btn-submit">Login</button>
        </form><br>
        <!-- Google Login Button -->
        <div class="google-login">
          <a href="google_login.php"><i class="fab fa-google"></i> Sign in with Google</a>
        </div>
        <div class="additional-links">
          <p><a href="forgot_password.php">Forgot Password?</a></p><br>
          <p>Don't have an account? <a href="register.php"> Register here</a></p>
          <p>
          <br>
          <br>
            <a href="../admin/admin_login.php" style="display:inline-block; padding: 8px 16px; background: #6c757d; color: #fff; border-radius: 5px; text-decoration: none;">Login as Admin</a>
          </p>
        </div>
      </div>
    </div>
  </main>
  
  <!-- Footer -->
  <footer>
    &copy; <?php echo date("Y"); ?> weshPAY. All rights reserved.
  </footer>
</body>
</html>
