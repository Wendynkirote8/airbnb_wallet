<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host_id = intval($_POST["host_id"]);
    $amount = floatval($_POST["amount"]);
    if ($amount <= 0) {
        die("Invalid amount.");
    }

    $guest_id = $_SESSION["user_id"];

    try {
        $pdo->beginTransaction();

        // Check guest's wallet balance
        $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$guest_id]);
        $guest_wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$guest_wallet || $guest_wallet["balance"] < $amount) {
            die("Insufficient funds.");
        }

        // Check if host has a wallet
        $stmt = $pdo->prepare("SELECT wallet_id FROM wallets WHERE user_id = ?");
        $stmt->execute([$host_id]);
        $host_wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$host_wallet) {
            die("Host wallet not found.");
        }

        // Deduct from guest wallet
        $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE wallet_id = ?");
        $stmt->execute([$amount, $guest_wallet["wallet_id"]]);

        // Credit host wallet
        $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE wallet_id = ?");
        $stmt->execute([$amount, $host_wallet["wallet_id"]]);

        // Log transactions
        $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status) 
                               VALUES (?, ?, 'payment', 'completed')");
        $stmt->execute([$guest_wallet["wallet_id"], $amount]);

        $stmt = $pdo->prepare("INSERT INTO transactions (wallet_id, amount, transaction_type, status) 
                               VALUES (?, ?, 'payment_received', 'completed')");
        $stmt->execute([$host_wallet["wallet_id"], $amount]);

        $pdo->commit();
        echo "Payment successful!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage(); 
        // fgbrkd
    }
}
?>
