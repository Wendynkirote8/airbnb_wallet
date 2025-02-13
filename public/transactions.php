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
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h1 class="page-title">Transaction History</h1>
    <div class="details">
        <div class="recentOrders">
            <div class="cardHeader">
                <h2>Recent Transactions</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Amount ($)</td>
                        <td>Type</td>
                        <td>Status</td>
                        <td>Date</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction) { ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                            <td><?= htmlspecialchars($transaction['amount']) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                            <td>
                                <span class="status <?= strtolower(htmlspecialchars($transaction['status'])) ?>">
                                    <?= htmlspecialchars($transaction['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/navbarroot.php'; ?>
