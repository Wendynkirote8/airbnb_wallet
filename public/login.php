<?php
session_start();
require '../config/db_connect.php';

$error = ""; // Initialize variable

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - weshPAY</title>
  <meta name="description" content="Login to your weshPAY account and manage your Airbnb finances seamlessly.">
  <link rel="icon" href="path/to/favicon.ico" />
  
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  
  <style>
    /* CSS Reset */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    /* Base Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(45deg, #f7f7f7, #eaeaea);
      min-height: 100vh;
      color: #333;
      display: flex;
      flex-direction: column;
      scroll-behavior: smooth;
    }
    /* Header */
    header {
      padding: 20px 50px;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #333;
    }
    nav a {
      text-decoration: none;
      color: #FF5A5F;
      font-size: 16px;
      margin-left: 20px;
      transition: color 0.3s;
    }
    nav a:hover {
      color: #e14b50;
    }
    /* Landscape Container */
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 50px;
    }
    .landscape-container {
      display: flex;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      overflow: hidden;
      max-width: 900px;
      width: 100%;
    }
    /* Left Image Section */
    .image-section {
      flex: 1;
      background: #ccc; /* Fallback background */
    }
    .image-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    /* Right Form Section */
    .form-section {
      flex: 1;
      padding: 40px;
    }
    .form-section h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #555;
    }
    .form-group input[type="email"],
    .form-group input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }
    .form-group input[type="email"]:focus,
    .form-group input[type="password"]:focus {
      outline: none;
      border-color: #FF5A5F;
    }
    .btn-submit {
      display: block;
      width: 100%;
      padding: 12px;
      background: #FF5A5F;
      color: #fff;
      text-align: center;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    .btn-submit:hover {
      background: #e14b50;
      transform: scale(1.02);
    }
    .additional-links {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }
    .additional-links a {
      color: #FF5A5F;
      text-decoration: none;
      transition: color 0.3s;
    }
    .additional-links a:hover {
      color: #e14b50;
    }
    /* Footer */
    footer {
      text-align: center;
      padding: 20px;
      background: #eaeaea;
      font-size: 14px;
      color: #555;
    }
    /* Responsive: Stack on small screens */
    @media (max-width: 768px) {
      .landscape-container {
        flex-direction: column;
      }
      .image-section {
        height: 200px;
      }
    }
  </style>
</head>
<body>
  <!-- Header / Navigation -->
  <header>
    <div class="logo">weshPAY</div>
    <nav>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    </nav>
  </header>
  
  <!-- Main Content -->
  <main>
    <div class="landscape-container">
      <!-- Left Image Section -->
      <div class="image-section">
        <!-- Replace with your desired landscape image for login -->
        <img src="../uploads/landing_page/login_landscape.jpg" alt="Login Landscape">
      </div>
      <!-- Right Form Section -->
      <div class="form-section">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
          <div class="alert" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn-submit">Login</button>
        </form>
        <div class="additional-links">
          <p><a href="forgot_password.php">Forgot Password?</a></p>
          <p><a href="register.php">Don't have an account? Register here</a></p>
          <p><a href="../admin/admin_login.php" style="display:inline-block; padding: 8px 16px; background: #6c757d; color: #fff; border-radius: 5px; text-decoration: none;">Login as Admin</a></p>
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
