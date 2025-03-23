<?php
session_start();
require_once '../config/db_connect.php';

// Check if admin is logged in; if not, redirect to admin login page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
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
          <h2>Welcome, Admin!</h2>
          <p>Select an option from the sidebar to manage the site.</p>
          <!-- Additional admin dashboard summary or analytics can be added here -->
        </div>
      </div>
    </div>
  </div>
</body>
</html>
