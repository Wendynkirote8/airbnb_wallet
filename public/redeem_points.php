<?php
session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Initialize messages
$successMessage = $errorMessage = "";

// Process redeem points form submission on the same page
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["redeem_points"])) {
    $pointsToRedeem = intval($_POST["redeem_points"]);

    // Fetch user's current loyalty points
    $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $loyaltyData = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentPoints = $loyaltyData ? intval($loyaltyData["points"]) : 0;

    if ($pointsToRedeem < 10) {
        $errorMessage = "Minimum redeemable points is 10.";
    } elseif ($pointsToRedeem > $currentPoints) {
        $errorMessage = "You do not have enough points to redeem that amount.";
    } else {
        // Conversion: 10 points = Ksh 1
        $redeemAmount = $pointsToRedeem / 10;

        try {
            $pdo->beginTransaction();

            // 1. Update wallet: add redeemed amount to user's wallet.
            $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$wallet) {
                // If no wallet exists, create one with initial balance equal to redeemAmount.
                $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, ?)");
                $stmt->execute([$user_id, $redeemAmount]);
                $wallet_id = $pdo->lastInsertId();
            } else {
                $wallet_id = $wallet["wallet_id"];
                // Update wallet balance by adding the redeemed amount.
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE wallet_id = ?");
                $stmt->execute([$redeemAmount, $wallet_id]);
            }

            // 2. Update loyalty_points: subtract the redeemed points and update last_updated.
            $newPoints = $currentPoints - $pointsToRedeem;
            $stmt = $pdo->prepare("UPDATE loyalty_points SET points = ?, last_updated = NOW() WHERE user_id = ?");
            $stmt->execute([$newPoints, $user_id]);

            // 3. Insert transaction record for the redemption.
            // Here, the 'amount' field stores the redeemed money (Ksh) equivalent.
            $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status, created_at) VALUES (?, ?, 'redeem', 'completed', NOW())");
            $stmt->execute([$wallet_id, $redeemAmount]);

            $pdo->commit();
            $successMessage = "Redeem successful! You redeemed $pointsToRedeem points (Ksh " . number_format($redeemAmount, 2) . ").";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

// After processing, re-fetch the updated loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0;
$equivalent_money = $userPoints / 10;

// Fetch user details for header display
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/customer01.jpg";

// Fetch redeem history (only 'redeem' transactions)
try {
    $stmt = $pdo->prepare("
        SELECT t.transaction_id, t.amount, t.created_at 
        FROM transactions t 
        JOIN wallets w ON t.wallet_id = w.wallet_id 
        WHERE w.user_id = ? AND t.transaction_type = 'redeem'
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $redeems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total redeemed amount (in Ksh)
    $totalRedeemed = 0;
    foreach ($redeems as $redeem) {
        $totalRedeemed += $redeem['amount'];
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>weshPAY - Redeem Loyalty Points</title>

  <!-- Unified Styles (same as your new dashboard) -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style>
    /* Additional styling for the redeem page */
    .history-table {
      margin-top: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .history-table h2 {
      margin-bottom: 15px;
      color: #2a2185;
    }
    .table-responsive table {
      width: 100%;
      border-collapse: collapse;
    }
    .table-responsive th, .table-responsive td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    .table-responsive th {
      background-color: #f2f2f2;
      color: #333;
    }
    /* Message styling */
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
</head>
<body>
  <!-- =============== Sidebar Navigation ================ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <?php include '../includes/navbar.php'; ?>
  </aside>

  <!-- =============== Main Content ================ -->
  <div class="main-content">
    <!-- Top Header -->
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
        <h1>Redeem Your Loyalty Points</h1>
        <p>
          You have <span class="points_redeem"><?php echo number_format($userPoints); ?></span> points.
          <br>
          Equivalent Value: <span class="money_redeem">Ksh <?php echo number_format($equivalent_money, 2); ?></span>
        </p>
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

      <!-- Redeem Form Card -->
      <div class="form-card">
        <form action="" method="POST" class="deposit-form">
          <label for="redeem_points">Enter Points to Redeem:</label>
          <input 
            type="number" 
            name="redeem_points" 
            id="redeem_points" 
            placeholder="Enter Points to Redeem" 
            min="10" 
            max="<?php echo $userPoints; ?>" 
            required
          />
          <button type="submit">Redeem Points</button>
        </form>
      </div>

      <!-- Redeem History Section -->
      <div class="history-table">
        <h2>Redeem History</h2>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>Points Redeemed</th>
                <th>Amount (Ksh)</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($redeems)): ?>
                <?php foreach ($redeems as $redeem): ?>
                  <?php 
                    // Calculate points redeemed based on conversion rate (10 points = Ksh 1)
                    $pointsRedeemed = $redeem['amount'] * 10; 
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($redeem['transaction_id']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($pointsRedeemed)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($redeem['amount'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($redeem['created_at']); ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr>
                  <td colspan="3" style="text-align: right; font-weight: bold;">Total Redeemed Amount:</td>
                  <td style="font-weight: bold;"><?php echo "Ksh " . number_format($totalRedeemed, 2); ?></td>
                </tr>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align: center;">No redeem transactions found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>

    <!-- Optional footer or additional navigation -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

  <!-- =========== Scripts =========  -->
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
