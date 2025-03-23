<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$password_hash, $user_id]);
            $success = "Password updated successfully for user ID: " . htmlspecialchars($user_id);
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
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
  <title>Change User Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include '../includes/navbar.php'; ?>
  <div class="container mt-4">
    <h2>Change User Password</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <div class="mb-3">
      <label for="userSelect" class="form-label">Select User</label>
      <select id="userSelect" class="form-select" onchange="populateUserId(this)">
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
      <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" id="new_password" required>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
  </div>
  <script>
    function populateUserId(selectElement) {
      document.getElementById('user_id').value = selectElement.value;
    }
  </script>
  <?php include '../includes/navbarroot.php'; ?>
</body>
</html>
