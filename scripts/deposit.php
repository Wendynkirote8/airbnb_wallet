<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = floatval($_POST["amount"]);
    if ($amount <= 0) {
        die("Invalid amount.");
    }

    $user_id = $_SESSION["user_id"];

    try {
        $pdo->beginTransaction();

        // Check if user has a wallet
        $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            // Create a wallet if not exists
            $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, 0)");
            $stmt->execute([$user_id]);
            $wallet_id = $pdo->lastInsertId();
        } else {
            $wallet_id = $wallet["wallet_id"];
        }

        // Update wallet balance
        $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE wallet_id = ?");
        $stmt->execute([$amount, $wallet_id]);

        // Log transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status) 
                               VALUES (?, ?, 'deposit', 'completed')");
        $stmt->execute([$wallet_id, $amount]);

        $pdo->commit();
        echo "Deposit successful!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
