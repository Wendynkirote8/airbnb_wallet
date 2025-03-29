<?php
session_start();
require '../config/db_connect.php';

$error = "";
$success = "";

// If the user is already logged in, redirect them away from register
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // (Same as your existing registration logic)
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Profile picture logic, etc...
    // ...

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, profile_picture) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $password, $profile_picture]);
        $newUserId = $pdo->lastInsertId();
        $success = "Registration successful!";
        
        // Automatically log in the user
        $_SESSION["user_id"] = $newUserId;
        $_SESSION["full_name"] = $full_name;
        
        // Redirect to MFA setup
        header("Location: enable_2fa.php");
        exit();
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
  <title>Register - weshPAY</title>
  <meta name="description" content="Register for weshPAY, your modern Airbnb E-Wallet solution." />
  <link rel="icon" href="path/to/favicon.ico" />
  
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/login_reg.css">
</head>
<body>
  <!-- Header / Navigation -->
  <header>
    <div class="logo">weshPAY</div>
    <nav>
      <a href="login.php">Sign In</a>
      <a href="register.php">Register</a>
    </nav>
  </header>
  
  <!-- Main Content -->
  <main>
    <div class="landscape-container">
      <!-- Left Image Section -->
      <div class="image-section">
        <img src="../uploads/landing_page/real-estate.jpg" alt="Registration Landscape" />
      </div>
      <!-- Right Form Section -->
      <div class="form-section">
        <h2>Register</h2>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Traditional Registration Form -->
        <form action="register.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
          </div>
          <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
          </div>
          <button type="submit" class="btn-submit">Register</button>
        </form>

        <!-- OR: Continue with Google -->
        <div style="text-align: center; margin: 1rem 0;">
          <p>Or register with:</p>
          <a href="google_register.php" 
             style="display: inline-block; padding: 10px 20px; background: #4285F4; color: #fff; border-radius: 5px; text-decoration: none;">
            <i class="fab fa-google"></i> Continue with Google
          </a>
        </div>

        <div class="message">
          <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    &copy; <?php echo date("Y"); ?> weshPAY. All rights reserved.
  </footer>
</body>
</html>
