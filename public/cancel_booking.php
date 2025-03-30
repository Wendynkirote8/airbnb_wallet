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

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Delete the booking record so it no longer appears in your list
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);

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
    header("Location: room_details.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $pdo->rollBack();
    die("Cancellation failed: " . $e->getMessage());
}
?>
