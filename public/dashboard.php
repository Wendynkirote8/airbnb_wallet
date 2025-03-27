<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user details
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) ? "../uploads/" . $user["profile_picture"] : "../assets/imgs/default-user.png";

// Get wallet balance
$stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $wallet ? $wallet["balance"] : 0.00;

// Get user's loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$points_data = $stmt->fetch(PDO::FETCH_ASSOC);
$points = $points_data ? $points_data["points"] : 0;
$equivalent_money = $points / 10; // 10 points = 1 Ksh

// === Chart Data: Deposits and Bookings ===

// Query deposits: Sum of deposit amounts grouped by month (format YYYY-MM)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(amount) AS total_deposit 
    FROM transactions 
    WHERE transaction_type = 'deposit' 
      AND wallet_id IN (SELECT wallet_id FROM wallets WHERE user_id = ?)
    GROUP BY month 
    ORDER BY month ASC
");
$stmt->execute([$user_id]);
$depositData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query bookings: Count bookings grouped by month (format YYYY-MM)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(booking_date, '%Y-%m') AS month, COUNT(*) AS total_bookings 
    FROM bookings 
    WHERE user_id = ? 
    GROUP BY month 
    ORDER BY month ASC
");
$stmt->execute([$user_id]);
$bookingData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Merge months from both datasets
$allMonths = [];
foreach ($depositData as $row) {
    $allMonths[$row['month']] = true;
}
foreach ($bookingData as $row) {
    $allMonths[$row['month']] = true;
}
ksort($allMonths);
$labels = array_keys($allMonths);

// Prepare data arrays for deposits and bookings
$depositValues = [];
$bookingValues = [];
foreach ($labels as $month) {
    // Find deposit value for this month
    $found = false;
    foreach ($depositData as $row) {
        if ($row['month'] === $month) {
            $depositValues[] = floatval($row['total_deposit']);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $depositValues[] = 0;
    }
    
    // Find booking count for this month
    $found = false;
    foreach ($bookingData as $row) {
        if ($row['month'] === $month) {
            $bookingValues[] = intval($row['total_bookings']);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $bookingValues[] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard - Modern Redesign</title>
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>WeshPAY</h2>
    </div>
    <?php include '../includes/navbar.php'; ?>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search...">
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <section class="overview">
      <div class="welcome-card">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p>Your wallet balance is <strong>Ksh <?php echo number_format($balance, 2); ?></strong></p>
      </div>
      <div class="info-cards">
        <div class="info-card">
          <ion-icon name="wallet-outline"></ion-icon>
          <div>
            <h3>Ksh <?php echo number_format($balance, 2); ?></h3>
            <p>Wallet Balance</p>
          </div>
        </div>
        <div class="info-card">
          <ion-icon name="gift-outline"></ion-icon>
          <div>
            <h3><?php echo number_format($points); ?> pts</h3>
            <p>Loyalty Points</p>
          </div>
        </div>
        <div class="info-card">
          <ion-icon name="cash-outline"></ion-icon>
          <div>
            <h3>Ksh <?php echo number_format($equivalent_money, 2); ?></h3>
            <p>Redeemable Amount</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
      <h3>Transaction Trends</h3>
      <canvas id="transactionChart"></canvas>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>

  <script>
  var labels = <?php echo json_encode($labels); ?>;
  var depositValues = <?php echo json_encode($depositValues); ?>;
  var bookingValues = <?php echo json_encode($bookingValues); ?>;

  var ctx = document.getElementById('transactionChart').getContext('2d');
  var transactionChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Deposits (Ksh)',
          data: depositValues,
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          fill: false,
          tension: 0.1,
          yAxisID: 'y'       // Link to the default (left) y-axis
        },
        {
          label: 'Bookings',
          data: bookingValues,
          borderColor: 'rgba(153, 102, 255, 1)',
          backgroundColor: 'rgba(153, 102, 255, 0.2)',
          fill: false,
          tension: 0.1,
          yAxisID: 'y1'      // Link to the second (right) y-axis
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          type: 'linear',
          position: 'left',
          title: {
            display: true,
            text: 'Deposits (Ksh)'
          }
        },
        y1: {
          beginAtZero: true,
          type: 'linear',
          position: 'right',
          title: {
            display: true,
            text: 'Number of Bookings'
          },
          grid: {
            drawOnChartArea: false // keeps the grid lines from overlapping
          }
        }
      }
    }
  });
</script>

  <script src="../assets/js/dashboard_new.js"></script>
</body>
</html>
