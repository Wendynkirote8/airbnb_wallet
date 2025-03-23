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

// Fetch user's available loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0; // Default to 0 if no record found

$message = "";
$messageClass = "";

// Handle POST submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = isset($_POST["amount"]) ? floatval($_POST["amount"]) : 0;
    $action = $_POST["action"] ?? "";

    if ($amount <= 0) {
        $message = "Invalid amount.";
        $messageClass = "error-message";
    } else {
        try {
            $pdo->beginTransaction();

            // Get user's wallet
            $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$wallet || $wallet["balance"] < $amount) {
                $message = "Insufficient balance.";
                $messageClass = "error-message";
            } else {
                // Redeem logic
                if ($action === "redeem") {
                    if ($userPoints >= $amount) {
                        // Deduct points
                        $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points - ? WHERE user_id = ?");
                        $stmt->execute([$amount, $user_id]);

                        // 10 points = 1 Ksh discount
                        $discount = $amount / 10;
                        $amount -= $discount;
                        $message = "You redeemed $amount points for a Ksh $discount discount!<br>";
                        $messageClass = "success-message";
                    } else {
                        $message = "Not enough points to redeem. Proceeding with regular withdrawal.<br>";
                        $messageClass = "error-message";
                    }
                }

                // Deduct from wallet
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE wallet_id = ?");
                $stmt->execute([$amount, $wallet["wallet_id"]]);

                // Log transaction
                $stmt = $pdo->prepare(
                    "INSERT INTO transactions (wallet_id, amount, transaction_type, status) 
                     VALUES (?, ?, 'withdrawal', 'completed')"
                );
                $stmt->execute([$wallet["wallet_id"], $amount]);

                $pdo->commit();
                $message .= "Withdrawal of Ksh $amount successful!";
                if (empty($messageClass)) {
                    $messageClass = "success-message";
                }

                // Refresh loyalty points
                $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0;
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
            $messageClass = "error-message";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Withdraw Funds - Modern UI</title>

  <!-- Use the same CSS as your new dashboard -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>

  <!-- Sidebar Navigation (Same layout as dashboard) -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a></li>
        <li><a href="transactions.php"><ion-icon name="receipt-outline"></ion-icon> Transaction History</a></li>
        <li><a href="deposit.php"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <!-- Highlight this page as active -->
        <li><a href="withdraw_new.php" class="active"><ion-icon name="cash-outline"></ion-icon> Withdraw Funds</a></li>
        <li><a href="redeem_points.php"><ion-icon name="gift-outline"></ion-icon> Redeem Points</a></li>
        <li><a href="messages.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Messages</a></li>
        <li><a href="settings.php"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Top Header (Same style as dashboard) -->
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
        <h1>Withdraw Funds</h1>
        <p>Available Loyalty Points: <strong><?php echo $userPoints; ?></strong></p>
      </div>

      <!-- Display Messages -->
      <?php if (!empty($message)): ?>
        <div class="message-container <?php echo $messageClass; ?>">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- Withdrawal Form in a Card -->
      <div class="form-card">
        <form action="withdraw_new.php" method="POST" class="deposit-form">
          <label for="amount">Amount</label>
          <input 
            type="number" 
            name="amount" 
            id="amount" 
            placeholder="Enter withdrawal amount" 
            required 
          />

          <div class="button-row">
            <button type="submit" name="action" value="withdraw">Withdraw</button>
            <button type="submit" name="action" value="redeem">Redeem Points</button>
          </div>
        </form>
      </div>
    </section>

    <!-- Footer or Navbar Root -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

</body>
</html>
