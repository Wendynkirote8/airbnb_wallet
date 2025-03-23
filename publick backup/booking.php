<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if listing_id is provided
if (!isset($_GET['listing_id'])) {
    die("Listing ID is required.");
}

$listing_id = $_GET['listing_id'];

// Get listing details
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
$stmt->execute([$listing_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die("Listing not found.");
}

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];

    // Insert booking into database
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, listing_id, checkin_date, checkout_date) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $listing_id, $checkin, $checkout])) {
        echo "<script>alert('Booking successful!'); window.location.href='my_bookings.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error processing booking.');</script>";
    }
}
?>

<?php include '../includes/navbar.php'; ?>

<!-- ======================= Booking Section ================== -->
<div class="booking-container">
    <h2>Book: <?php echo htmlspecialchars($listing['title']); ?></h2>
    <img src="<?php echo htmlspecialchars($listing['image_url']); ?>" alt="Listing Image">
    <p><?php echo htmlspecialchars($listing['description']); ?></p>
    <p><strong>Price:</strong> Ksh <?php echo number_format($listing['price'], 2); ?>/night</p>
    
    <form method="post">
        <label for="checkin">Check-in Date:</label>
        <input type="date" name="checkin" required>

        <label for="checkout">Check-out Date:</label>
        <input type="date" name="checkout" required>

        <button type="submit">Confirm Booking</button>
    </form>
</div>

<?php include '../includes/navbarroot.php'; ?>
