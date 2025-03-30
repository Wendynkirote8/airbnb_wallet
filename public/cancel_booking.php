<?php
session_start();
require_once '../config/db_connect.php'; // Ensure $pdo is set up

// Ensure user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];

// Get booking ID from the query string
if (!isset($_GET['id'])) {
    die("No booking specified.");
}
$booking_id = $_GET['id'];

// Verify that this booking belongs to the user and is pending
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$booking) {
    die("Booking not found or cannot be cancelled.");
}

$total_cost = $booking['total_cost'];
$room_id = $booking['room_id'];    // Assuming you have room_id in bookings
$days = $booking['days'];
$booking_date = $booking['booking_date'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Instead of deleting the booking record, update its status to 'canceled'
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'canceled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);

    // Insert a log record into booking_logs to capture this cancellation action
    $stmt = $pdo->prepare("
        INSERT INTO booking_logs (booking_id, user_id, room_id, days, total_cost, booking_date, action, status)
        VALUES (?, ?, ?, ?, ?, ?, 'canceled', 'canceled')
    ");
    $stmt->execute([$booking_id, $user_id, $room_id, $days, $total_cost, $booking_date]);

    // Retrieve the current wallet balance
    $stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$wallet) {
        throw new Exception("Wallet not found.");
    }
    $new_balance = $wallet['balance'] + $total_cost;

    // Update the wallet balance (refund the booking amount)
    $stmt = $pdo->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
    $stmt->execute([$new_balance, $user_id]);

    // Commit transaction
    $pdo->commit();

    // Set a session message for the refund amount
    $_SESSION['cancel_message'] = "Booking cancelled. Refund of ksh. " . number_format($total_cost, 2) . " has been credited to your wallet.";

    // Redirect back to the room details or dashboard page
    header("Location: my_bookings.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $pdo->rollBack();
    die("Cancellation failed: " . $e->getMessage());
}
?>
