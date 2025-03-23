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
  <title>User Dashboard</title>
  <!-- External CSS -->
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <!-- Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <?php include '../includes/navbar.php'; ?>

  <main>
    <!-- ======================= Welcome Section ================== -->
    <section class="welcome-section">
      <div class="user-profile">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
      </div>
      <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
      <p>Your wallet balance: <strong>Ksh <?php echo number_format($balance, 2); ?></strong></p>
    </section>

    <!-- ======================= Dashboard Cards ================== -->
    <section class="cardBox">
      <a class="card" href="transactions.php">
        <div>
          <div class="cardName">Transaction History</div>
        </div>
        <div class="iconBx">
          <ion-icon name="receipt-outline"></ion-icon>
        </div>
      </a>

      <a class="card" href="deposit.php">
        <div>
          <div class="cardName">Deposit Funds</div>
        </div>
        <div class="iconBx">
          <ion-icon name="card-outline"></ion-icon>
        </div>
      </a>

      <a class="card" href="withdraw.php">
        <div>
          <div class="cardName">Withdraw Funds</div>
        </div>
        <div class="iconBx">
          <ion-icon name="cash-outline"></ion-icon>
        </div>
      </a>

      <div class="card">
        <div>
          <div class="numbers">Ksh <?php echo number_format($balance, 2); ?></div>
          <div class="cardName">Wallet Balance</div>
        </div>
        <div class="iconBx">
          <ion-icon name="wallet-outline"></ion-icon>
        </div>
      </div>

      <div class="card">
        <div>
          <div class="numbers"><?php echo number_format($points); ?> pts</div>
          <div class="cardName">Loyalty Points</div>
        </div>
        <div class="iconBx">
          <ion-icon name="gift-outline"></ion-icon>
        </div>
      </div>

      <div class="card">
        <div>
          <div class="numbers">Ksh <?php echo number_format($equivalent_money, 2); ?></div>
          <div class="cardName">Redeemable Amount</div>
        </div>
        <div class="iconBx">
          <ion-icon name="cash-outline"></ion-icon>
        </div>
      </div>

      <a class="card" href="redeem_points.php">
        <div>
          <div class="cardName">Redeem Points</div>
        </div>
        <div class="iconBx">
          <ion-icon name="card-outline"></ion-icon>
        </div>
      </a>
    </section>

    <!-- ======================= Chart Section ================== -->
    <section class="chart-section">
      <h3>Transaction Trends</h3>
      <canvas id="transactionChart"></canvas>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </main>

  <!-- External JavaScript -->
  <script src="../assets/js/dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
