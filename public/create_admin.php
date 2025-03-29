<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only logged-in admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email is already used
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT admin_id FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "Email is already registered.";
        }
    }

    // If no errors, insert new admin record
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        if ($stmt->execute([
            ':username'     => $username,
            ':email'        => $email,
            ':password_hash'=> $password_hash
        ])) {
            $success = "New admin account created successfully!";
        } else {
            $errors[] = "An error occurred while creating the admin account.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Admin - Wesh AirBNB Pay</title>
  <!-- External CSS (if any) -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- Internal CSS for this page -->
  <style>
    /* Centered Form Container */
    .form-container {
      max-width: 600px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }
    .form-container h2 {
      margin-bottom: 1rem;
      color: var(--blue);
    }
    /* Alert messages */
    .custom-alert {
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 4px;
      font-weight: 500;
      line-height: 1.3;
    }
    .custom-alert.alert-error {
      background-color: #f8d7da;
      color: #842029;
    }
    .custom-alert.alert-success {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    /* Form fields */
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--blue);
    }
    .form-group input {
      width: 100%;
      padding: 0.7rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    .form-group input:focus {
      border-color: var(--blue);
      outline: none;
    }
    /* Submit Button */
    .custom-btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      margin-top: 0.5rem;
      border: none;
      border-radius: 4px;
      background-color: var(--blue);
      color: var(--white);
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .custom-btn:hover {
      background-color: var(--blue2);
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <ul>
        <li>
          <a href="#">
            <span class="icon">
              <ion-icon name="home-outline"></ion-icon>
            </span>
            <span class="title">Wesh Pay</span>
          </a>
        </li>
        <li>
          <a onclick="window.location.href='admin_dashboard.php';" style="cursor: pointer;">
            <span class="icon">
              <ion-icon name="grid-outline"></ion-icon>
            </span>
            <span class="title">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="admin_add_room.php">
            <span class="icon">
              <ion-icon name="bed-outline"></ion-icon>
            </span>
            <span class="title">Add Room</span>
          </a>
        </li>
        <li>
          <a href="admin_manage_rooms.php">
            <span class="icon">
              <ion-icon name="list-outline"></ion-icon>
            </span>
            <span class="title">Manage Rooms</span>
          </a>
        </li>
        <li>
          <a href="admin_manage_users.php">
            <span class="icon">
              <ion-icon name="people-outline"></ion-icon>
            </span>
            <span class="title">Manage Users</span>
          </a>
        </li>
        <li>
          <a href="admin_change_password.php">
            <span class="icon">
              <ion-icon name="lock-closed-outline"></ion-icon>
            </span>
            <span class="title">Change Password</span>
          </a>
        </li>
        <li>
          <a href="admin_transactions.php">
            <span class="icon">
              <ion-icon name="receipt-outline"></ion-icon>
            </span>
            <span class="title">Transactions</span>
          </a>
        </li>
        <li>
          <a onclick="window.location.href='../public/logout_admin.php';" style="cursor: pointer;">
            <span class="icon">
              <ion-icon name="log-out-outline"></ion-icon>
            </span>
            <span class="title">Sign Out</span>
          </a>
        </li>
      </ul>
    </div>
    <!-- Main Content Area -->
    <div class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="toggle">
          <ion-icon name="menu-outline"></ion-icon>
        </div>
        <div class="search">
          <label>
            <input type="text" placeholder="Search here">
            <ion-icon name="search-outline"></ion-icon>
          </label>
        </div>
        <div class="user" onclick="toggleProfileDropdown()">
          <img src="../assets/imgs/default-profile.png" alt="Admin Profile">
        </div>
        <!-- Profile Dropdown -->
        <div id="profileDropdown" class="dropdown">
          <div class="dropdown-content">
            <img src="../assets/imgs/default-profile.png" alt="Profile Picture">
            <p class="user-name"><strong><?php echo htmlspecialchars($username); ?></strong></p>
            <p class="user-email"><?php echo htmlspecialchars($email); ?></p>
            <button onclick="window.location.href='admin_edit_profile.php'">Edit Profile</button>
            <button onclick="window.location.href='../public/logout_admin.php'">Logout</button>
          </div>
        </div>
      </div>
      <!-- Page Content -->
      <div class="content">
        <h2>Create New Admin</h2>
        
        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
          <div class="custom-alert alert-error">
            <?php foreach ($errors as $error): ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        
        <!-- Display Success -->
        <?php if (!empty($success)): ?>
          <div class="custom-alert alert-success">
            <p><?php echo htmlspecialchars($success); ?></p>
          </div>
        <?php endif; ?>
        
        <!-- Create Admin Form -->
        <div class="form-container">
          <form action="create_admin.php" method="POST">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" placeholder="Enter admin username" required>
            </div>
            <div class="form-group">
              <label for="email">Admin Email</label>
              <input type="email" id="email" name="email" placeholder="Enter admin email" required>
            </div>
            <div class="form-group">
              <label for="password">Admin Password</label>
              <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirm Admin Password</label>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="custom-btn">Create Admin</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    function toggleProfileDropdown() {
      document.getElementById("profileDropdown").classList.toggle("show");
    }
    window.onclick = function(event) {
      if (!event.target.closest(".user")) {
        document.getElementById("profileDropdown").classList.remove("show");
      }
    }
  </script>
</body>
</html>
