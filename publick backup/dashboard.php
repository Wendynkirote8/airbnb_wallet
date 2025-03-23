<?php
session_start();
require '../config/db_connect.php';

// Redirect to login if not authenticated
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard - Modern Redesign</title>

  <!-- External CSS -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>MyBrand</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a></li>
        <li><a href="transactions.php"><ion-icon name="receipt-outline"></ion-icon> Transaction History</a></li>
        <li><a href="deposit.php"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <li><a href="withdraw.php"><ion-icon name="cash-outline"></ion-icon> Withdraw Funds</a></li>
        <li><a href="redeem_points.php"><ion-icon name="gift-outline"></ion-icon> Redeem Points</a></li>
        <li><a href="messages.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Messages</a></li>
        <li><a href="settings.php"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Top Header -->
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

    <!-- Overview / Welcome Section -->
    <section class="overview">
      <div class="welcome-card">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p>Your wallet balance is <strong>Ksh <?php echo number_format($balance, 2); ?></strong></p>
      </div>

      <!-- Info Cards -->
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

    <!-- Quick Action Buttons -->
    <section class="actions">
      <div class="action-card">
        <a href="transactions.php">
          <ion-icon name="receipt-outline"></ion-icon>
          <span>Transaction History</span>
        </a>
      </div>
      <div class="action-card">
        <a href="deposit.php">
          <ion-icon name="card-outline"></ion-icon>
          <span>Deposit Funds</span>
        </a>
      </div>
      <div class="action-card">
        <a href="withdraw.php">
          <ion-icon name="cash-outline"></ion-icon>
          <span>Withdraw Funds</span>
        </a>
      </div>
      <div class="action-card">
        <a href="redeem_points.php">
          <ion-icon name="gift-outline"></ion-icon>
          <span>Redeem Points</span>
        </a>
      </div>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
      <h3>Transaction Trends</h3>
      <canvas id="transactionChart"></canvas>
    </section>

    <!-- Example Footer or Navbar Root -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

  <!-- External JavaScript -->
  <script src="../assets/js/dashboard_new.js"></script>
</body>
</html>
