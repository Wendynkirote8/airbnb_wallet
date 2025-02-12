<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user details
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get wallet balance
$stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $wallet ? $wallet["balance"] : 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; text-align: center; }
        h1 { color: #333; }
        .balance { font-size: 24px; font-weight: bold; color: green; }
        .links { margin-top: 20px; }
        .links a { display: block; margin: 10px; padding: 10px; background: #007bff; color: #fff; text-decoration: none; }
        .links a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user["full_name"]); ?>!</h1>
        <p>Your Wallet Balance: <span class="balance">$<?php echo number_format($balance, 2); ?></span></p>

        <div class="links">
            <a href="deposit.html">Deposit Funds</a>
            <a href="withdraw.html">Withdraw Funds</a>
            <a href="payment.html">Make a Payment</a>
            <a href="../scripts/transactions.php">Transaction History</a>
            <a href="logout.php" style="background: red;">Logout</a>
        </div>
    </div>
</body>
</html>
