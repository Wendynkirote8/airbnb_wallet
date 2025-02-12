<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("SELECT t.transaction_id, t.amount, t.transaction_type, t.status, t.created_at 
                           FROM transactions t 
                           JOIN wallets w ON t.wallet_id = w.wallet_id 
                           WHERE w.user_id = ? 
                           ORDER BY t.created_at DESC");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaction History</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Transaction History</h1>
    <table>
        <tr><th>ID</th><th>Amount ($)</th><th>Type</th><th>Status</th><th>Date</th></tr>
        <?php foreach ($transactions as $transaction) {
            echo "<tr>
                    <td>{$transaction['transaction_id']}</td>
                    <td>{$transaction['amount']}</td>
                    <td>{$transaction['transaction_type']}</td>
                    <td>{$transaction['status']}</td>
                    <td>{$transaction['created_at']}</td>
                  </tr>";
        } ?>
    </table>
</body>
</html>
