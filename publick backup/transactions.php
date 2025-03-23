<?php
session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

// Fetch user details (for header display)
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/default-user.png";

// Fetch transactions
try {
    $stmt = $pdo->prepare("
        SELECT t.transaction_id, t.amount, t.transaction_type, t.status, t.created_at 
        FROM transactions t 
        JOIN wallets w ON t.wallet_id = w.wallet_id 
        WHERE w.user_id = ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Transaction History - Modern UI</title>

  <!-- Use the same CSS as your redesigned dashboard -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>

  <!-- Sidebar Navigation (same style as the new dashboard) -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>MyBrand</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard_new.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a></li>
        <!-- Highlight the Transaction History link as active -->
        <li><a href="transactions_new.php" class="active"><ion-icon name="receipt-outline"></ion-icon> Transaction History</a></li>
        <li><a href="deposit.php"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <li><a href="withdraw_new.php"><ion-icon name="cash-outline"></ion-icon> Withdraw Funds</a></li>
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

    <!-- Page Content -->
    <section class="overview">
      <!-- Title Card -->
      <div class="welcome-card">
        <h1>Transaction History</h1>
        <p>Review all your recent transactions below.</p>
      </div>

      <!-- Transactions Table Card -->
      <div class="table-card">
        <div class="table-header">
          <h2>Recent Transactions</h2>
        </div>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Amount (Ksh)</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                    <td>
                      <span class="status <?php echo strtolower(htmlspecialchars($transaction['status'])); ?>">
                        <?php echo htmlspecialchars($transaction['status']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center;">No transactions found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Include your footer or navbar root if needed -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

</body>
</html>
