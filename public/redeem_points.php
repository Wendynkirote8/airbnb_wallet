<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = intval($_POST["booking_id"]);
    $redeem_points = intval($_POST["redeem_points"]);
    $user_id = $_SESSION["user_id"];

    try {
        $pdo->beginTransaction();

        // Check user's available points
        $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"];

        if ($userPoints < $redeem_points) {
            die("Not enough points.");
        }

        // Calculate discount (10 points = $1)
        $discount = $redeem_points / 10;

        // Deduct points
        $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points - ? WHERE user_id = ?");
        $stmt->execute([$redeem_points, $user_id]);

        // Apply discount to booking
        $stmt = $pdo->prepare("UPDATE bookings SET amount = amount - ? WHERE booking_id = ?");
        $stmt->execute([$discount, $booking_id]);

        $pdo->commit();
        echo "You redeemed $redeem_points points for a $$discount discount!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
