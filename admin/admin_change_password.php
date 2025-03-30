<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db_connect.php'; // Ensure the file creates a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Generate a CSRF token if one isn't already present.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

// Handle form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify the CSRF token.
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        // Retrieve and validate form inputs.
        $user_id          = $_POST["user_id"] ?? '';
        $new_password     = $_POST["new_password"] ?? '';
        $confirm_password = $_POST["confirm_password"] ?? '';
        
        if (empty($user_id)) {
            $error = "No user selected.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($new_password) < 8) { // Enforce a minimum password length.
            $error = "Password must be at least 8 characters long.";
        } else {
            // Hash the new password and update the user record.
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id");
                $stmt->bindParam(':password_hash', $password_hash);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $success = "Password updated successfully for user ID: " . htmlspecialchars($user_id);
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch list of users for selection.
try {
    $stmt = $pdo->query("SELECT user_id, full_name, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Change User Password</title>
  <link rel="stylesheet" href="../assets/css/admin_style.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>
    <!-- Main Content Area -->
    <div class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
        <div class="search">
          <input type="text" placeholder="Search here">
        </div>
        <div class="user">
          <img src="../assets/imgs/default-profile.png" alt="Admin Profile">
        </div>
      </div>
      <!-- Content -->
      <div class="content">
        <h2>Change User Password</h2>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <div class="form-container">
          <div class="form-group">
            <label for="userSelect">Select User</label>
            <select id="userSelect" onchange="populateUserId(this)">
              <option value="">-- Select User --</option>
              <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['user_id']; ?>">
                  <?php echo htmlspecialchars($user['full_name']) . ' (' . htmlspecialchars($user['email']) . ')'; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <form method="POST" action="admin_change_password.php">
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" name="new_password" id="new_password" required minlength="8">
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
            </div>
            <button type="submit" class="custom-btn">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    function populateUserId(selectElement) {
      document.getElementById('user_id').value = selectElement.value;
    }
  </script>
</body>
</html>
