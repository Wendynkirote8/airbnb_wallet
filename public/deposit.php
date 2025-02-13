<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$successMessage = $errorMessage = ""; // Variables to hold messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = floatval($_POST["amount"]);
    $user_id = $_SESSION["user_id"];

    if ($amount < 10) {
        $errorMessage = "Minimum deposit is $10.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Check if user has a wallet
            $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$wallet) {
                // Create a wallet if not exists and add deposit amount
                $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, ?)");
                $stmt->execute([$user_id, $amount]);
                $wallet_id = $pdo->lastInsertId();
            } else {
                $wallet_id = $wallet["wallet_id"];
                // Update Wallet Balance
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE wallet_id = ?");
                $stmt->execute([$amount, $wallet_id]);
            }

            // 2. Log Transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status, created_at) 
                                   VALUES (?, ?, 'deposit', 'completed', NOW())");
            $stmt->execute([$wallet_id, $amount]);

            // 3. Calculate Loyalty Points (5 points per $100)
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
?>

<?php include '../includes/navbar.php'; ?>

<div class="deposit-container">
    <h2>Deposit Funds</h2>

    <!-- Display success or error messages -->
    <?php if (!empty($successMessage)): ?>
        <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php elseif (!empty($errorMessage)): ?>
        <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form action="deposit.php" method="POST" class="deposit-form" onsubmit="return validateDeposit()">
        <input type="number" id="amount" name="amount" placeholder="Enter amount (Min: $10)" required>
        <button type="submit">Deposit</button>
    </form>
</div>

<script>
    function validateDeposit() {
        let amount = document.getElementById('amount').value;
        if (amount < 10) {
            alert("Minimum deposit is $10.");
            return false;
        }
        return true;
    }
</script>

<?php include '../includes/navbarroot.php'; ?>
