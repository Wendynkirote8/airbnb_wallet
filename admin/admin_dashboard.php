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

// Pending Requests (sample)
$pendingRequests = 3;

// === Improved Graph Data ===
// We'll fetch monthly data for new rooms, new users, and total transactions.
// For demonstration purposes, we assume that each table has a created_at date field.

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

// Merge months from all datasets
$allMonths = [];
foreach ($roomsDataRaw as $row) {
    $allMonths[$row['month']] = true;
}
foreach ($usersDataRaw as $row) {
    $allMonths[$row['month']] = true;
}
foreach ($transactionsDataRaw as $row) {
    $allMonths[$row['month']] = true;
}
ksort($allMonths);
$labels = array_keys($allMonths);

// Prepare data arrays with default value 0 for missing months
$roomsData = [];
$usersData = [];
$transactionsData = [];

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
    if (!$found) {
        $roomsData[] = 0;
    }
    // Users
    $found = false;
    foreach ($usersDataRaw as $row) {
        if ($row['month'] == $month) {
            $usersData[] = intval($row['users_count']);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $usersData[] = 0;
    }
    // Transactions (in Ksh)
    $found = false;
    foreach ($transactionsDataRaw as $row) {
        if ($row['month'] == $month) {
            $transactionsData[] = floatval($row['total_transactions']);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $transactionsData[] = 0;
    }
}
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
    /* Container styling */
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
      max-width: 900px;
      margin: 2rem auto;
      background: var(--white);
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    /* Latest Transactions Section */
    .transactions-container {
      max-width: 900px;
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
            <p>Ksh <?php echo number_format($totalTransactions); ?></p>
          </div>
          <div class="card">
            <h3>Pending Requests</h3>
            <p><?php echo $pendingRequests; ?></p>
          </div>
        </div>
        
        <!-- Improved Graph Section -->
        <div class="graph-container">
          <h3>Platform Trends (Monthly)</h3>
          <canvas id="trendsChart"></canvas>
        </div>
        
        <!-- Latest Transactions (Placeholder) -->
        <div class="transactions-container">
          <h3>Latest Transactions</h3>
          <div class="transaction-item">Transaction #101 - Ksh 250</div>
          <div class="transaction-item">Transaction #102 - Ksh 180</div>
          <div class="transaction-item">Transaction #103 - Ksh 320</div>
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
  
  <!-- Chart.js Script for Improved Trends Chart -->
  <script>
    // Prepare PHP arrays for Chart.js
    var labels = <?php echo json_encode($labels); ?>;
    var roomsData = <?php echo json_encode($roomsData); ?>;
    var usersData = <?php echo json_encode($usersData); ?>;
    var transactionsData = <?php echo json_encode($transactionsData); ?>;
    
    var ctx = document.getElementById('trendsChart').getContext('2d');
    var trendsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Rooms Added',
            data: roomsData,
            borderColor: 'rgba(42, 33, 133, 1)', // a blue tone
            backgroundColor: 'rgba(42, 33, 133, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
          },
          {
            label: 'New Users',
            data: usersData,
            borderColor: 'rgba(255, 159, 64, 1)', // orange tone
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
          },
          {
            label: 'Transaction Amount (Ksh)',
            data: transactionsData,
            borderColor: 'rgba(75, 192, 192, 1)', // greenish tone
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
              text: 'Transaction Amount (Ksh)'
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
