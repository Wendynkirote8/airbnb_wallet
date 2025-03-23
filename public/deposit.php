<?php
session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

// Variables to hold messages
$successMessage = $errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = floatval($_POST["amount"]);
    $user_id = $_SESSION["user_id"];

    // Validate minimum deposit
    if ($amount < 10) {
        $errorMessage = "Minimum deposit is Ksh 10.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Check if user has a wallet
            $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$wallet) {
                // Create a wallet if none exists
                $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, ?)");
                $stmt->execute([$user_id, $amount]);
                $wallet_id = $pdo->lastInsertId();
            } else {
                $wallet_id = $wallet["wallet_id"];
                // Update wallet balance
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE wallet_id = ?");
                $stmt->execute([$amount, $wallet_id]);
            }

            // 2. Log Transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status, created_at) 
                                   VALUES (?, ?, 'deposit', 'completed', NOW())");
            $stmt->execute([$wallet_id, $amount]);

            // 3. Calculate Loyalty Points (5 points per Ksh 100)
            $points = floor($amount / 100) * 5;

            // 4. Check if user already has loyalty points
            $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $existingPoints = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingPoints) {
                $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points + ? WHERE user_id = ?");
                $stmt->execute([$points, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO loyalty_points (user_id, points) VALUES (?, ?)");
                $stmt->execute([$user_id, $points]);
            }

            $pdo->commit();
            $successMessage = "Deposit successful! You earned $points points.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

// (Optional) Fetch user details for header display
$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/default-user.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>weshPAY - Deposit Funds</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ======= Unified Styles (Same as your new dashboard) ======= -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Additional styles for messages (if not in your CSS) -->
  <style>
    .message-container {
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      text-align: center;
    }
    .success-message {
      background-color: #d4edda;
      color: #155724;
    }
    .error-message {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <!-- =============== Sidebar Navigation ================ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
        <li><a href="transactions.php"><ion-icon name="receipt-outline"></ion-icon> Transaction History</a></li>
        <!-- Highlight deposit as active -->
        <li><a href="deposit.php" class="active"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <li><a href="withdraw.php"><ion-icon name="cash-outline"></ion-icon> Withdraw Funds</a></li>
        <li><a href="redeem_points.php"><ion-icon name="gift-outline"></ion-icon> Redeem Points</a></li>
        <li><a href="messages.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Messages</a></li>
        <li><a href="settings.php"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- =============== Main Content ================ -->
  <div class="main-content">
    <!-- Topbar (Header) -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search here" />
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User Profile">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Page Content -->
    <section class="overview">
      <!-- Title Card -->
      <div class="welcome-card">
        <h1>Deposit Funds</h1>
        <p>Add money to your wallet securely.</p>
      </div>

      <!-- Display success or error messages -->
      <?php if (!empty($successMessage)): ?>
        <div class="message-container success-message">
          <?php echo htmlspecialchars($successMessage); ?>
        </div>
      <?php elseif (!empty($errorMessage)): ?>
        <div class="message-container error-message">
          <?php echo htmlspecialchars($errorMessage); ?>
        </div>
      <?php endif; ?>

      <!-- Deposit Form -->
      <div class="form-card">
        <form action="deposit.php" method="POST" class="deposit-form" onsubmit="return validateDeposit()">
          <label for="amount">Enter Amount (Min: Ksh 10):</label>
          <input 
            type="number" 
            id="amount" 
            name="amount" 
            placeholder="Enter amount (Min: Ksh 10)" 
            required 
          />
          <button type="submit">Deposit</button>
        </form>
      </div>
    </section>
  </div>

  <!-- =========== Scripts =========  -->
  <script>
    function validateDeposit() {
      let amount = document.getElementById('amount').value;
      if (amount < 10) {
        alert("Minimum deposit is Ksh 10.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
