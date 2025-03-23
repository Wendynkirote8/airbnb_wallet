<?php
session_start();
require_once '../config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin details (using the columns: admin_id, username, email)
$stmt = $pdo->prepare("SELECT username, email FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION["admin_id"]]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $admin['username'] ?? 'Admin';
$email = $admin['email'] ?? 'admin@example.com';

// Use a default profile picture since none exists in your schema.
$profile_picture = "../assets/imgs/default-profile.png";

// Sample data for summary cards (in a real app, fetch from your database)

// Query total rooms
$stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms");
$totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Query total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Query total transactions (sum of amounts)
$stmt = $pdo->query("SELECT SUM(amount) as total FROM transactions");
$totalTransactionsData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalTransactions = $totalTransactionsData['total'] ?? 0;

// // Query pending requests
// $stmt = $pdo->query("SELECT COUNT(*) as total FROM requests WHERE status = 'pending'");
// $pendingRequests = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


// $totalRooms = 12;
// $totalUsers = 45;
// $totalTransactions = 2345;
$pendingRequests = 3;

// Sample data for Chart.js (e.g., Rooms Added Over the Last 6 Months)
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$roomsAdded = [2, 3, 1, 4, 2, 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Wesh AirBNB Pay</title>
  <!-- External CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Ionicons (for icons) -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Internal CSS for Dashboard Enhancements -->
  <style>
    /* Center welcome and content */
    .content {
      text-align: center;
      padding: 20px;
    }
    .content h2 {
      margin-bottom: 1rem;
      color: var(--blue);
    }
    .content p {
      margin-bottom: 1.5rem;
    }
    /* Create Admin Button */
    .create-admin-container {
      text-align: center;
      margin: 2rem 0;
    }
    .create-admin-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.85rem 1.8rem;
      background-color: var(--blue);
      color: var(--white);
      text-decoration: none;
      border-radius: 5px;
      font-size: 1.1rem;
      font-weight: bold;
      transition: background-color 0.3s, transform 0.3s;
    }
    .create-admin-btn:hover {
      background-color: var(--blue2);
      transform: scale(1.02);
    }
    /* Dashboard Summary Cards */
    .card-container {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin-top: 20px;
      justify-content: center;
    }
    .card {
      background: var(--white);
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      flex: 1 1 200px;
      padding: 1rem;
      text-align: center;
    }
    .card h3 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      color: var(--blue);
    }
    .card p {
      font-size: 1.2rem;
      color: var(--black2);
    }
    /* Graph Container */
    .graph-container {
      max-width: 800px;
      margin: 2rem auto;
      background: var(--white);
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    /* Latest Transactions Section */
    .transactions-container {
      max-width: 800px;
      margin: 2rem auto;
      text-align: left;
    }
    .transactions-container h3 {
      margin-bottom: 1rem;
      color: var(--blue);
    }
    .transaction-item {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }
    .transaction-item:last-child {
      border-bottom: none;
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
          <a onclick="window.location.href='admin_dashboard.php';" style="cursor: pointer;" class="active-link">
            <span class="icon">
              <ion-icon name="grid-outline"></ion-icon>
            </span>
            <span class="title">Dashboard</span>
          </a>
        </li>
        <li>
        <a href="create_admin.php">
          <span class="icon">
            <ion-icon name="person-add-outline"></ion-icon>
          </span>
          <span class="title">Create Admin</span>
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
          <a onclick="window.location.href='../admin/logout_admin.php';" style="cursor: pointer;">
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
          <img src="<?php echo $profile_picture; ?>" alt="Admin Profile">
        </div>
        <!-- Profile Dropdown -->
        <div id="profileDropdown" class="dropdown">
          <div class="dropdown-content">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <p class="user-name"><strong><?php echo htmlspecialchars($username); ?></strong></p>
            <p class="user-email"><?php echo htmlspecialchars($email); ?></p>
            <button onclick="window.location.href='admin_edit_profile.php'">Edit Profile</button>
            <button onclick="window.location.href='../admin/logout_admin.php'">Logout</button>
          </div>
        </div>
      </div>
      
      <!-- Dashboard Content -->
      <div class="content">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Select an option from the navigation to manage the site.</p>
        
        <!-- Dashboard Summary Cards -->
        <div class="card-container">
          <div class="card">
            <h3>Total Rooms</h3>
            <p><?php echo $totalRooms; ?></p>
          </div>
          <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
          </div>
          <div class="card">
            <h3>Transactions</h3>
            <p>ksh<?php echo number_format($totalTransactions); ?></p>
          </div>
          <div class="card">
            <h3>Pending Requests</h3>
            <p><?php echo $pendingRequests; ?></p>
          </div>
        </div>
        
        <!-- Graph Section -->
        <div class="graph-container">
          <h3>Rooms Added Over Last 6 Months</h3>
          <canvas id="roomsChart"></canvas>
        </div>
        
        <!-- Latest Transactions (Placeholder) -->
        <div class="transactions-container">
          <h3>Latest Transactions</h3>
          <div class="transaction-item">Transaction #101 - $250</div>
          <div class="transaction-item">Transaction #102 - $180</div>
          <div class="transaction-item">Transaction #103 - $320</div>
          <!-- Add more items as needed -->
        </div>
        
        <!-- Option to Create a New Admin -->
        <div class="create-admin-container">
          <a href="create_admin.php" class="create-admin-btn">
            <ion-icon name="person-add-outline"></ion-icon>
            Create New Admin
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Chart.js Script -->
  <script>
    // Get the context of the canvas element we want to select
    const ctx = document.getElementById('roomsChart').getContext('2d');
    const roomsChart = new Chart(ctx, {
      type: 'line', // You can choose 'bar', 'line', etc.
      data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
          label: 'Rooms Added',
          data: <?php echo json_encode($roomsAdded); ?>,
          backgroundColor: 'rgba(42, 33, 133, 0.2)', // var(--blue) with transparency
          borderColor: 'rgba(42, 33, 133, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  </script>
  
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
  <script src="../assets/js/main.js"></script>
</body>
</html>
