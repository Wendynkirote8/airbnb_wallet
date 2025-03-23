<?php
session_start();
require_once '../config/db_connect.php'; // This file creates a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch users from the database using PDO.
try {
    $sql = "SELECT user_id, full_name, email, phone, profile_picture FROM users ORDER BY user_id ASC";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - Admin Dashboard</title>
  <!-- Internal CSS for Wesh AirBNB Pay Theme -->
  <style>
    /* Reset & Globals */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Ubuntu", sans-serif;
    }
    :root {
      --blue: #2a2185;
      --blue2: #1c193f;
      --white: #fff;
      --gray: #f5f5f5;
      --black1: #222;
      --black2: #999;
    }
    body {
      background: var(--gray);
    }
    /* Layout */
    .container {
      display: flex;
      min-height: 100vh;
    }
    /* Navigation Sidebar */
    .navigation {
      width: 300px;
      background: var(--blue);
      padding: 20px;
      overflow-y: auto;
    }
    .navigation h3 {
      color: var(--white);
      text-align: center;
      margin-bottom: 20px;
    }
    .navigation ul {
      list-style: none;
    }
    .navigation ul li {
      margin-bottom: 20px;
    }
    .navigation ul li a {
      color: var(--white);
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 4px;
      transition: background 0.3s, color 0.3s;
    }
    .navigation ul li a:hover,
    .navigation ul li a.active-link {
      background: var(--white);
      color: var(--blue);
    }
    /* Main Content */
    .main {
      flex: 1;
      background: var(--white);
      padding: 20px;
    }
    /* Topbar */
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .toggle {
      font-size: 24px;
      cursor: pointer;
    }
    .search input {
      width: 300px;
      padding: 8px;
      border: 1px solid var(--black2);
      border-radius: 4px;
    }
    .user {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      cursor: pointer;
    }
    .user img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    /* Content Header */
    .content h2 {
      margin-bottom: 20px;
      color: var(--blue);
    }
    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table thead {
      background: var(--blue);
      color: var(--white);
    }
    table thead th {
      padding: 10px;
      text-align: center;
    }
    table tbody tr {
      border-bottom: 1px solid #ddd;
      transition: background 0.3s;
    }
    table tbody tr:hover {
      background: var(--gray);
    }
    table tbody td {
      padding: 10px;
      text-align: center;
    }
    /* Profile Image */
    .profile-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
    /* Action Buttons */
    .action-btn {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .action-btn.edit {
      background: var(--blue);
      color: var(--white);
    }
    .action-btn.delete {
      background: red;
      color: var(--white);
    }
  </style>
  <!-- Ionicons (for icons) -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <h3>Admin Panel</h3>
      <ul>
        <li><a href="admin_dashboard.php">Dashboard Home</a></li>
        <li><a href="admin_add_room.php">Add Room</a></li>
        <li><a href="admin_manage_rooms.php">Manage Rooms</a></li>
        <li><a href="admin_manage_users.php" class="active-link">Manage Users</a></li>
        <li><a href="admin_change_password.php">Change User Password</a></li>
        <li><a href="admin_transactions.php">Transactions</a></li>
      </ul>
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
                <th>Profile Picture</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td>
                  <?php if (!empty($user['profile_picture'])): ?>
                    <img src="../<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                  <?php else: ?>
                    <span>No Image</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td>
                  <a href="admin_edit_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn edit">Edit</a>
                  <a href="admin_delete_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No users found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
