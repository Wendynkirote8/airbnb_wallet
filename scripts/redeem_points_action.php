<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user's current loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0;

// Calculate equivalent money (10 points = Ksh 1)
$equivalent_money = $userPoints / 10;
?>



<?php include '../includes/navbar.php'; ?>

    <div class="deposit-container">
        <h2>Redeem Your Loyalty Points</h2>
        <p>You have <span class="points_redeem"><?php echo number_format($userPoints); ?></span> points.</p>
        <p>Equivalent Value: <span class="money_redeem">Ksh <?php echo number_format($equivalent_money, 2); ?></span></p>

        <form action="../scripts/redeem_points_action.php" method="POST">
            <input type="number" name="redeem_points" id="redeem_points" placeholder="Enter Points to Redeem" min="10" max="<?php echo $userPoints; ?>" required>
            <button type="submit">Redeem Points</button>
        </form>
    </div>

<?php include '../includes/navbarroot.php'; ?>


