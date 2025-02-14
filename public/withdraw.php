<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$message = "";
$messageClass = "";
$user_id = $_SESSION["user_id"];

// Fetch user's available loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0; // Default to 0 if no record found

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = isset($_POST["amount"]) ? floatval($_POST["amount"]) : 0;
    $action = isset($_POST["action"]) ? $_POST["action"] : "";

    if ($amount <= 0) {
        $message = "Invalid amount.";
        $messageClass = "error-message";
    } else {
        try {
            $pdo->beginTransaction();

            // Get user's wallet balance
            $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$wallet || $wallet["balance"] < $amount) {
                $message = "Insufficient balance.";
                $messageClass = "error-message";
            } else {
                // Check user's loyalty points if redeeming
                if ($action == "redeem") {
                    if ($userPoints >= $amount) {
                        // Deduct points
                        $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points - ? WHERE user_id = ?");
                        $stmt->execute([$amount, $user_id]);

                        // Apply discount (10 points = $1)
                        $discount = $amount / 10;
                        $amount -= $discount;
                        $message = "You redeemed $amount points for a $$discount discount!<br>";
                        $messageClass = "success-message";
                    } else {
                        $message = "Not enough points to redeem. Proceeding with regular withdrawal.<br>";
                        $messageClass = "error-message";
                    }
                }

                // Deduct balance from wallet
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE wallet_id = ?");
                $stmt->execute([$amount, $wallet["wallet_id"]]);

                // Log transaction
                $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status) 
                                    VALUES (?, ?, 'withdrawal', 'completed')");
                $stmt->execute([$wallet["wallet_id"], $amount]);

                $pdo->commit();
                $message .= "Withdrawal of $$amount successful!";
                $messageClass = "success-message";

                // Refresh loyalty points after redemption
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

<?php include '../includes/navbar.php'; ?>

<div class="deposit-container">
    <h2>Withdraw Funds</h2>
    
    <!-- Display available loyalty points -->
    <p class="loyalty-points">Available Loyalty Points: <?php echo $userPoints; ?></p>

    <?php if (!empty($message)): ?>
        <div class="message-container <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="withdraw.php" method="POST" class="deposit-form">
        <input type="number" name="amount" placeholder="Amount" required>
        <div><button type="submit" name="action" value="withdraw">Withdraw</button></div>
        <div><button type="submit" name="action" value="redeem">Redeem Points</button></div>
    </form>
</div>

<?php include '../includes/navbarroot.php'; ?>

