<?php
session_start();
require '../config/db_connect.php';

$error = ""; // Initialize the variable to prevent 'undefined variable' warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try {
        $stmt = $pdo->prepare("SELECT user_id, full_name, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
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
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f8f9fa;
    }
    .login-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2 class="text-center">Login</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3">
      <a href="forgot_password.php">Forgot Password?</a>
    </div>
    <div class="text-center mt-2">
      <a href="register.php">Don't have an account? Click here to register</a>
    </div>
    <!-- Added "Login as Admin" button -->
    <div class="text-center mt-2">
      <a href="../admin/admin_login.php" class="btn btn-secondary">Login as Admin</a>
    </div>
  </div>
</body>
</html>
