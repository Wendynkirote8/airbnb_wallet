<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user's bookings
$stmt = $pdo->prepare("
    SELECT b.id, l.title, l.image_url, b.checkin_date, b.checkout_date 
    FROM bookings b
    JOIN listings l ON b.listing_id = l.id
    WHERE b.user_id = ?
    ORDER BY b.checkin_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/navbar.php'; ?>

<!-- ======================= My Bookings Section ================== -->
<h2>My Bookings</h2>
<div class="bookings-list">
    <?php foreach ($bookings as $booking): ?>
        <div class="booking-card">
            <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="Listing Image">
            <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
            <p><strong>Check-in:</strong> <?php echo $booking['checkin_date']; ?></p>
            <p><strong>Check-out:</strong> <?php echo $booking['checkout_date']; ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/navbarroot.php'; ?>
