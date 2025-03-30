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

// Query summary cards data
// Total Rooms
$stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms");
$totalRooms = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Transactions (sum of amounts)
$stmt = $pdo->query("SELECT SUM(amount) as total FROM transactions");
$totalTransactionsData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalTransactions = $totalTransactionsData['total'] ?? 0;

// Earnings (sum of amounts from the earning table)
$stmt = $pdo->query("SELECT SUM(amount) as total FROM earning");
$totalEarningsData = $stmt->fetch(PDO::FETCH_ASSOC);
$totalEarnings = $totalEarningsData['total'] ?? 0;

// Pending Booking Requests
$stmt = $pdo->prepare("
    SELECT b.id as booking_id, b.user_id, b.room_id, b.days, b.total_cost, b.booking_date, b.status, r.name as room_name 
    FROM bookings b
    LEFT JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'pending'
    ORDER BY b.booking_date ASC
");
$stmt->execute();
$pendingBookingsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pendingRequests = count($pendingBookingsData);

// === Improved Graph Data ===

// Rooms Added per Month
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS rooms_count
    FROM rooms
    GROUP BY month
    ORDER BY month ASC
");
$stmt->execute();
$roomsDataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// New Users Registered per Month
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS users_count
    FROM users
    GROUP BY month
    ORDER BY month ASC
");
$stmt->execute();
$usersDataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total Transactions per Month
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(amount) AS total_transactions
    FROM transactions
    GROUP BY month
    ORDER BY month ASC
");
$stmt->execute();
$transactionsDataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Earnings per Month (from earning table)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(amount) AS total_earnings
    FROM earning
    GROUP BY month
    ORDER BY month ASC
");
$stmt->execute();
$earningsDataRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Merge months from all datasets
$allMonths = [];
foreach ($roomsDataRaw as $row) { $allMonths[$row['month']] = true; }
foreach ($usersDataRaw as $row) { $allMonths[$row['month']] = true; }
foreach ($transactionsDataRaw as $row) { $allMonths[$row['month']] = true; }
foreach ($earningsDataRaw as $row) { $allMonths[$row['month']] = true; }
ksort($allMonths);
$labels = array_keys($allMonths);

// Prepare data arrays with default value 0 for missing months
$roomsData = [];
$usersData = [];
$transactionsData = [];
$earningsData = [];

foreach ($labels as $month) {
    // Rooms
    $found = false;
    foreach ($roomsDataRaw as $row) {
        if ($row['month'] == $month) {
            $roomsData[] = intval($row['rooms_count']);
            $found = true;
            break;
        }
    }
    if (!$found) { $roomsData[] = 0; }
    // Users
    $found = false;
    foreach ($usersDataRaw as $row) {
        if ($row['month'] == $month) {
            $usersData[] = intval($row['users_count']);
            $found = true;
            break;
        }
    }
    if (!$found) { $usersData[] = 0; }
    // Transactions (in Ksh)
    $found = false;
    foreach ($transactionsDataRaw as $row) {
        if ($row['month'] == $month) {
            $transactionsData[] = floatval($row['total_transactions']);
            $found = true;
            break;
        }
    }
    if (!$found) { $transactionsData[] = 0; }
    // Earnings (in Ksh)
    $found = false;
    foreach ($earningsDataRaw as $row) {
        if ($row['month'] == $month) {
            $earningsData[] = floatval($row['total_earnings']);
            $found = true;
            break;
        }
    }
    if (!$found) { $earningsData[] = 0; }
}

// Additional admin feature: recent earnings (last 5 fee records)
$stmt = $pdo->prepare("SELECT earning_id, amount, created_at FROM earning ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recentEarnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Wesh AirBNB Pay</title>
  <link rel="stylesheet" href="../assets/css/admin_style.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Global Reset and Base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f8;
      color: #333;
      overflow: hidden;
    }
    a { text-decoration: none; color: inherit; }

    /* Container Layout */
    .container {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    .navigation {
      width: 250px;
      background: #2a2185;
      color: #fff;
      padding: 20px;
      flex-shrink: 0;
      overflow-y: auto;
    }
    .navigation a {
      display: block;
      margin: 15px 0;
      padding: 10px 15px;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .navigation a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    /* Main Area */
    .main {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .topbar {
      background: #fff;
      padding: 15px 25px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #e0e0e0;
    }
    .topbar .toggle {
      cursor: pointer;
      font-size: 1.5rem;
    }
    .topbar .search {
      flex-grow: 1;
      margin: 0 20px;
      position: relative;
    }
    .topbar .search input {
      width: 100%;
      padding: 10px 40px 10px 15px;
      border: 1px solid #ccc;
      border-radius: 20px;
      outline: none;
      transition: border 0.3s;
    }
    .topbar .search input:focus {
      border-color: #2a2185;
    }
    .topbar .search ion-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }
    .topbar .user {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      overflow: hidden;
      cursor: pointer;
    }
    .topbar .user img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .dropdown {
      position: absolute;
      top: 70px;
      right: 25px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 8px;
      display: none;
      z-index: 1000;
    }
    .dropdown.show {
      display: block;
    }
    .dropdown .dropdown-content {
      padding: 20px;
      text-align: center;
    }
    .dropdown .dropdown-content img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      margin-bottom: 10px;
    }
    .dropdown .dropdown-content p {
      margin: 5px 0;
    }
    .dropdown .dropdown-content button {
      margin-top: 10px;
      padding: 8px 15px;
      border: none;
      background: #2a2185;
      color: #fff;
      border-radius: 20px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .dropdown .dropdown-content button:hover {
      background: #1c193f;
    }

    /* Scrollable Content */
    .content {
      padding: 25px;
      overflow-y: auto;
      flex-grow: 1;
    }
    .content h2 {
      margin-bottom: 10px;
    }
    .quick-links {
      display: flex;
      gap: 20px;
      margin: 20px 0;
      flex-wrap: wrap;
    }
    .quick-links a {
      flex: 1;
      min-width: 150px;
      padding: 15px 20px;
      background: #2a2185;
      color: #fff;
      border-radius: 8px;
      text-align: center;
      transition: background 0.3s;
    }
    .quick-links a:hover {
      background: #1c193f;
    }
    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card h3 { margin-bottom: 10px; color: #2a2185; }
    
    /* Graph Section */
    .graph-container {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .graph-container h3 {
      margin-bottom: 20px;
      color: #2a2185;
      border-bottom: 2px solid #2a2185;
      padding-bottom: 10px;
    }
    
    /* Tables */
    .pending-bookings-container,
    .recent-earnings-container,
    .transactions-container {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table th,
    table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 0.95rem;
    }
    table th {
        background: #2a2185;
        color: #fff;
    }
    .transactions-container .transaction-item {
      padding: 12px;
      margin-bottom: 10px;
      background: #f9f9f9;
      border-left: 5px solid #2a2185;
    }
    .create-admin-container {
      text-align: center;
      margin: 30px 0;
    }
    .create-admin-btn {
      display: inline-block;
      padding: 15px 25px;
      background: #2a2185;
      color: #fff;
      border-radius: 8px;
      transition: background 0.3s;
    }
    .create-admin-btn:hover {
      background: #1c193f;
    }
  </style>
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
        <div class="toggle">
          <ion-icon name="menu-outline"></ion-icon>
        </div>
        <div class="search">
          <input type="text" placeholder="Search here">
          <ion-icon name="search-outline"></ion-icon>
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
      
      <!-- Scrollable Content Area -->
      <div class="content">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Select an option from the navigation to manage the site.</p>
        
        <!-- Quick Admin Links -->
        <div class="quick-links">
          <a href="admin_manage_rooms.php"><ion-icon name="home-outline"></ion-icon> Manage Rooms</a>
          <a href="admin_manage_users.php"><ion-icon name="people-outline"></ion-icon> Manage Users</a>
          <a href="admin_manage_bookings.php"><ion-icon name="calendar-outline"></ion-icon> Manage Bookings</a>
          <a href="admin_transactions.php"><ion-icon name="card-outline"></ion-icon> View Transactions</a>
        </div>
        
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
            <p>Ksh <?php echo number_format($totalTransactions); ?></p>
          </div>
          <div class="card">
            <h3>Pending Requests</h3>
            <p><?php echo $pendingRequests; ?></p>
          </div>
          <div class="card">
            <h3>Earnings</h3>
            <p>Ksh <?php echo number_format($totalEarnings); ?></p>
          </div>
        </div>
        
        <!-- Graph Section -->
        <div class="graph-container">
          <h3>Platform Trends (Monthly)</h3>
          <canvas id="trendsChart"></canvas>
        </div>
        
        <!-- Pending Booking Requests Section -->
        <div class="pending-bookings-container">
          <h2>Pending Booking Requests</h2>
          <?php if ($pendingRequests > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>User ID</th>
                  <th>Room</th>
                  <th>Days</th>
                  <th>Total Cost</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingBookingsData as $pending): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($pending['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($pending['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($pending['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($pending['days']); ?></td>
                    <td><?php echo "Ksh " . number_format($pending['total_cost'], 2); ?></td>
                    <td><?php echo htmlspecialchars($pending['booking_date']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No pending booking requests found.</p>
          <?php endif; ?>
        </div>
        
        <!-- Recent Earnings Section -->
        <div class="recent-earnings-container">
          <h2>Recent Earnings (Fees)</h2>
          <?php if (count($recentEarnings) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Earning ID</th>
                <th>Amount (Ksh)</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentEarnings as $earning): ?>
                <tr>
                  <td><?php echo htmlspecialchars($earning['earning_id']); ?></td>
                  <td><?php echo "Ksh " . number_format($earning['amount'], 2); ?></td>
                  <td><?php echo htmlspecialchars($earning['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
            <p>No recent earnings found.</p>
          <?php endif; ?>
        </div>
        
        <!-- Latest Transactions (Placeholder) -->
        <div class="transactions-container">
          <h3>Latest Transactions</h3>
          <div class="transaction-item">Transaction #101 - Ksh 250</div>
          <div class="transaction-item">Transaction #102 - Ksh 180</div>
          <div class="transaction-item">Transaction #103 - Ksh 320</div>
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
  
  <!-- Chart.js Script for Trends Chart -->
  <script>
    var labels = <?php echo json_encode($labels); ?>;
    var roomsData = <?php echo json_encode($roomsData); ?>;
    var usersData = <?php echo json_encode($usersData); ?>;
    var transactionsData = <?php echo json_encode($transactionsData); ?>;
    var earningsData = <?php echo json_encode($earningsData); ?>;
    
    var ctx = document.getElementById('trendsChart').getContext('2d');
    var trendsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Rooms Added',
            data: roomsData,
            borderColor: 'rgba(42, 33, 133, 1)',
            backgroundColor: 'rgba(42, 33, 133, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
          },
          {
            label: 'New Users',
            data: usersData,
            borderColor: 'rgba(255, 159, 64, 1)',
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
          },
          {
            label: 'Transaction Amount (Ksh)',
            data: transactionsData,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
          },
          {
            label: 'Earnings (Ksh)',
            data: earningsData,
            borderColor: 'rgba(153, 102, 255, 1)',
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
          }
        ]
      },
      options: {
        responsive: true,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: true
          }
        },
        scales: {
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: {
              display: true,
              text: 'Ksh Amount'
            },
            beginAtZero: true
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: {
              display: true,
              text: 'Count'
            },
            grid: {
              drawOnChartArea: false
            },
            beginAtZero: true
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
