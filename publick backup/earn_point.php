<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = intval($_POST["booking_id"]);
    $amount = floatval($_POST["amount"]);
    $user_id = $_SESSION["user_id"];

    try {
        $pdo->beginTransaction();

        // Calculate points (5 points per $100 spent)
        $points = floor($amount / 100) * 5;

        // Check if user already has loyalty points
        $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existingPoints = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingPoints) {
            // Update points
            $stmt = $pdo->prepare("UPDATE loyalty_points SET points = points + ? WHERE user_id = ?");
            $stmt->execute([$points, $user_id]);
        } else {
            // Create new loyalty record
            $stmt = $pdo->prepare("INSERT INTO loyalty_points (user_id, points) VALUES (?, ?)");
            $stmt->execute([$user_id, $points]);
        }

        // Mark booking as rewarded
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'rewarded' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);

        $pdo->commit();
        echo "You earned $points loyalty points!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>


<form action="earn_point.php" method="POST">
    <input type="number" name="booking_id" placeholder="Booking ID" required>
    <input type="number" name="amount" placeholder="Booking Amount" required>
    <button type="submit">Earn Points</button>
</form>