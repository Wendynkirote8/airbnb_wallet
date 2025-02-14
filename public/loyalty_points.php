<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

// Fetch user's loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$loyalty = $stmt->fetch(PDO::FETCH_ASSOC);

$points = $loyalty ? $loyalty["points"] : 0;
?>


<?php include '../includes/navbar.php'; ?>

<div class="loyalty-container">
    <div class="name">Your Loyalty Points</div>

    <?php if ($points > 0): ?>
        <p class="points-display">You have <strong><?php echo $points; ?></strong> loyalty points!</p>
    <?php else: ?>
        <p class="no-points">😔 You have no loyalty points yet. Deposit to start earning! 🚀</p>
    <?php endif; ?>

    <a href="deposit.php" class="deposit-link">Deposit Now</a>
</div>

<?php include '../includes/navbarroot.php'; ?>


