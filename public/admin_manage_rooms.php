<?php
session_start();
require_once '../config/db_connect.php';

// Check if admin is logged in; if not, redirect to admin login page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch rooms from the database.
$sql = "SELECT id, name, description, price, capacity FROM rooms ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Rooms - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: #fff;
      padding-top: 20px;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
    }
    .sidebar a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar">
        <h3 class="text-center">Admin Panel</h3>
        <ul class="nav flex-column">
          <li class="nav-item"><a href="admin_dashboard.php" class="nav-link">Dashboard Home</a></li>
          <li class="nav-item"><a href="admin_add_room.php" class="nav-link">Add Room</a></li>
          <li class="nav-item"><a href="admin_manage_rooms.php" class="nav-link">Manage Rooms</a></li>
          <li class="nav-item"><a href="admin_manage_users.php" class="nav-link">Manage Users</a></li>
          <li class="nav-item"><a href="admin_change_password.php" class="nav-link">Change User Password</a></li>
          <li class="nav-item"><a href="admin_transactions.php" class="nav-link">Transactions</a></li>
          <!-- Add more links as needed -->
        </ul>
      </div>
      
      <!-- Main Content -->
      <div class="col-md-10">
        <div class="p-4">
          <h2>Manage Rooms</h2>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered table-striped">
              <thead class="table-dark">
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
                <?php while($room = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($room['id']); ?></td>
                    <td><?php echo htmlspecialchars($room['name']); ?></td>
                    <td><?php echo htmlspecialchars($room['description']); ?></td>
                    <td><?php echo htmlspecialchars($room['price']); ?></td>
                    <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                    <td>
                      <!-- Replace the href values with the correct file names -->
                      <a href="admin_edit_room.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                      <a href="admin_delete_room.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No rooms found. <a href="admin_add_room.php">Add a new room</a>.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
