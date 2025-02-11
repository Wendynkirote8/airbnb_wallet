<?php
require '../config/db_connect.php';

function getOrCreateWallet($user_id) {
    global $pdo;
    
    // Check if wallet exists
    $stmt = $pdo->prepare("SELECT wallet_id FROM wallets WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet) {
        // Create a wallet with a balance of $0
        $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, 0.00)");
        $stmt->execute([$user_id]);
        return $pdo->lastInsertId();
    }

    return $wallet['wallet_id'];
}
?>
