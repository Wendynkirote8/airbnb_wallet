<?php
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
$profile_picture = $user && !empty($user["profile_picture"]) ? $user["profile_picture"] : "../assets/imgs/default-user.png";

// Get wallet balance
$stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $wallet ? $wallet["balance"] : 0.00;
?>

<?php include '../includes/navbar.php'; ?>

<!-- ======================= Welcome Section ================== -->
<div class="welcome-section">
    <div class="user-profile">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
    </div>
    <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
    <p>Your wallet balance: <strong>Ksh<?php echo number_format($balance, 2); ?></strong></p>
</div>

<!-- ======================= Cards ================== -->
<div class="cardBox">
    <div class="card" onclick="window.location.href='transactions.php';" style="cursor: pointer;">
        <div>
            <div class="cardName"><a href="transactions.php">Transaction History</a></div>
        </div>
        <div class="iconBx">
            <ion-icon name="receipt-outline"></ion-icon>
        </div>
    </div>

    <div class="card" onclick="window.location.href='deposit.php';" style="cursor: pointer;">
        <div>
            <div class="cardName"><a href="deposit.php">Deposit Funds</a></div>
        </div>
        <div class="iconBx">
            <ion-icon name="card-outline"></ion-icon>
        </div>
    </div>

    <div class="card" onclick="window.location.href='withdraw.php';" style="cursor: pointer;">
        <div>
            <div class="cardName"><a href="withdraw.php">Withdraw Funds</a></div>
        </div>
        <div class="iconBx">
            <ion-icon name="cash-outline"></ion-icon>
        </div>
    </div>

    <div class="card">
        <div>
            <div class="numbers"><span class="balance">Ksh<?php echo number_format($balance, 2); ?></span></div>
            <div class="cardName">Wallet Balance</div>
        </div>
        <div class="iconBx">
            <ion-icon name="wallet-outline"></ion-icon>
        </div>
    </div>
</div>

<?php include '../includes/navbarroot.php'; ?>
