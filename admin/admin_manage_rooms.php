<?php
session_start();
require_once '../config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch rooms using PDO.
$sql = "SELECT id, name, description, price, capacity FROM rooms ORDER BY id ASC";
$stmt = $pdo->query($sql);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Rooms - Admin Dashboard</title>
  <!-- Internal CSS for Wesh AirBNB Pay theme -->
  <style>
    
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
        <h2>Manage Rooms</h2>
        <?php if (count($rooms) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Room Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Capacity</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rooms as $room): ?>
              <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['name']); ?></td>
                <td><?php echo htmlspecialchars($room['description']); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td>
                  <a href="admin_edit_room.php?id=<?php echo $room['id']; ?>" class="action-btn edit">Edit</a>
                  <a href="admin_delete_room.php?id=<?php echo $room['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No rooms found. <a href="admin_add_room.php">Add a new room</a>.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
