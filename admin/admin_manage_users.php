<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch users using PDO.
$sql = "SELECT user_id, full_name, email, phone, role, created_at, profile_picture FROM users ORDER BY user_id ASC";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - Admin Dashboard</title>
  <!-- Internal CSS for Wesh AirBNB Pay theme -->
  <style>
    /* Container for aligning action buttons side by side */
    .action-buttons {
      display: inline-flex;
      gap: 8px;           /* Adjust spacing between buttons as needed */
      align-items: center;
      white-space: nowrap; /* Prevent wrapping if space is limited */
    }

    /* Base styling for action buttons (if not already defined) */
    .action-btn {
      display: inline-block;
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 4px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    /* Edit button styling */
    .action-btn.edit {
      background-color: #4CAF50;
      color: #fff;
    }
    .action-btn.edit:hover {
      background-color: #45a049;
    }

    /* Delete button styling */
    .action-btn.delete {
      background-color: #f44336;
      color: #fff;
    }
    .action-btn.delete:hover {
      background-color: #e53935;
    }
   
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 12px;
      text-align: left;
    }
    /* Modified table header styling */
    th {
      background-color:#2a2185; /* Change this to your desired header color */
      color: #fff;               /* Text color for header */
    }
  </style>
  <link rel="stylesheet" href="../assets/css/admin_style.css">
  <!-- Ionicons (for icons) -->
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
        <h2>Manage Users</h2>
        <?php if (count($users) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Profile Picture</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td>
                  <?php if (!empty($user['profile_picture'])): ?>
                    <img src="../uploads/users/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="max-width: 50px;">
                  <?php else: ?>
                    N/A
                  <?php endif; ?>
                </td>
                <td>
                  <div class="action-buttons">
                    <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn edit">Edit</a>
                    <a href="admin_delete_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No users found. <a href="admin_add_user.php">Add a new user</a>.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
