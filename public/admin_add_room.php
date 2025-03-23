<?php
session_start();
require_once '../config/db_connect.php';

// Check if admin is logged in; if not, redirect to admin login page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and validate form inputs
    $room_name = trim($_POST['room_name']);
    $room_description = trim($_POST['room_description']);
    $room_price = trim($_POST['room_price']);
    $room_capacity = trim($_POST['room_capacity']);

    if (empty($room_name)) {
        $errors[] = "Room name is required.";
    }
    if (empty($room_price) || !is_numeric($room_price)) {
        $errors[] = "A valid room price is required.";
    }
    if (empty($room_capacity) || !is_numeric($room_capacity)) {
        $errors[] = "A valid room capacity is required.";
    }
    
    // If no errors, proceed to insert the room into the database.
    if (empty($errors)) {
        // Assuming you have a table "rooms" with columns: id, name, description, price, capacity.
        $sql = "INSERT INTO rooms (name, description, price, capacity) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssdi", $room_name, $room_description, $room_price, $room_capacity);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Room added successfully!";
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Failed to prepare the SQL statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Room - Admin Dashboard</title>
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
          <h2>Add New Room</h2>
          <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                  <?php foreach ($errors as $error): ?>
                      <p><?php echo htmlspecialchars($error); ?></p>
                  <?php endforeach; ?>
              </div>
          <?php endif; ?>
          <?php if ($success): ?>
              <div class="alert alert-success">
                  <p><?php echo htmlspecialchars($success); ?></p>
              </div>
          <?php endif; ?>
          <form action="admin_add_room.php" method="POST">
            <div class="mb-3">
              <label for="room_name" class="form-label">Room Name</label>
              <input type="text" class="form-control" id="room_name" name="room_name" required>
            </div>
            <div class="mb-3">
              <label for="room_description" class="form-label">Room Description</label>
              <textarea class="form-control" id="room_description" name="room_description" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="room_price" class="form-label">Room Price</label>
              <input type="number" step="0.01" class="form-control" id="room_price" name="room_price" required>
            </div>
            <div class="mb-3">
              <label for="room_capacity" class="form-label">Room Capacity</label>
              <input type="number" class="form-control" id="room_capacity" name="room_capacity" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Room</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
