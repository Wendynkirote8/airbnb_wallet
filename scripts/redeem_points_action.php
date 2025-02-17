<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];
$redeem_points = intval($_POST["redeem_points"]);

if ($redeem_points < 10) {
    die("Minimum redeemable points are 10.");
}

try {
    $pdo->beginTransaction();

    // Fetch user's current points
    $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"];

    if ($userPoints < $redeem_points) {
        die("Not enough points.");
    }

    // Convert points to money (10 points = Ksh 1)
    $equivalent_money = $redeem_points / 10;

    // Deduct points
    $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points - ? WHERE user_id = ?");
    $stmt->execute([$redeem_points, $user_id]);

    // Add money to user's wallet
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ?");
    $stmt->execute([$equivalent_money, $user_id]);

    $pdo->commit();
    header("Location: ../public/redeem_points.php?success=1");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
