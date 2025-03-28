<?php
require '../config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Handle profile picture upload
    $profile_picture = "../assets/imgs/default-user.png"; // Default profile image

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $target_dir = "../uploads/"; // Ensure this directory exists
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]); // Unique file name
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file; // Save file path in database
        } else {
            $error = "Failed to upload profile picture.";
        }
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $password, $profile_picture]);
        $success = "Registration successful! <a href='login.php'>Login here</a>";
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
      color: #2a2185;
      font-size: 16px;
      margin-left: 20px;
      transition: color 0.3s;
    }
    nav a:hover {
      color: #1c193f;
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
    /* Left Side Image */
    .image-section {
      flex: 1;
      background: #ccc; /* Fallback background */
    }
    .image-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    /* Right Side Form */
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
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group input[type="file"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }
    .form-group input[type="text"]:focus,
    .form-group input[type="email"]:focus,
    .form-group input[type="password"]:focus,
    .form-group input[type="file"]:focus {
      outline: none;
      border-color: #2a2185;
    }
    .btn-submit {
      display: block;
      width: 100%;
      padding: 12px;
      background: #2a2185;
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
      background: #1c193f;
      transform: scale(1.02);
    }
    .message {
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
    }
    .alert {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
    }
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
    }
    .alert-success {
      background: #d4edda;
      color: #155724;
    }
    /* Footer */
    .footer {
      text-align: center;
      padding: 20px;
      background: #eaeaea;
      font-size: 14px;
      color: #555;
    }
    /* Responsive: Stack sections on small screens */
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
      <a href="login.php">Sign In</a>
      <a href="register.php">Register</a>
    </nav>
  </header>
  
  <!-- Main Content -->
  <main>
    <div class="landscape-container">
      <!-- Left Image Section -->
      <div class="image-section">
        <!-- Replace with your landscape image -->
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
