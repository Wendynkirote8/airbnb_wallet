<?php
session_start();
require '../config/db_connect.php';

// Fetch all listings from the database
$stmt = $pdo->query("SELECT * FROM listings ORDER BY rating DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/navbar.php'; ?>

<!-- ======================= Airbnb Listings Section ================== -->
<h2>Available Airbnb Listings</h2>
<div id="listings-container" class="listings-grid">
    <?php foreach ($listings as $listing): ?>
        <div class="listing-card">
            <img src="<?php echo htmlspecialchars($listing['image_url']); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
            <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
            <p><?php echo htmlspecialchars($listing['description']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>
            <p><strong>Rating:</strong> ⭐ <?php echo number_format($listing['rating'], 1); ?></p>
            <p><strong>Price:</strong> Ksh <?php echo number_format($listing['price'], 2); ?>/night</p>
            <button onclick="window.location.href='booking.php?listing_id=<?php echo $listing['id']; ?>'">Book Now</button>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function bookNow(listingId) {
        window.location.href = `booking.php?listing_id=${listingId}`;
    }
</script>

<?php include '../includes/navbarroot.php'; ?>
