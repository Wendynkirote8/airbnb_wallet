<?php
session_start();
require '../config/db_connect.php';

// Fetch all listings from the database, ordering by rating descending
$stmt = $pdo->query("SELECT * FROM listings ORDER BY rating DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Airbnb Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Grid layout for the listings */
        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .listing-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.3s;
            background-color: #fff;
        }
        .listing-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .listing-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .listing-card .card-body {
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Available Airbnb Listings</h2>
        <div id="listings-container" class="listings-grid">
            <?php if (!empty($listings)): ?>
                <?php foreach ($listings as $listing): ?>
                    <div class="listing-card">
                        <?php if (!empty($listing['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($listing['image_url']); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                        <?php else: ?>
                            <img src="../assets/imgs/default-room.png" alt="Default Listing">
                        <?php endif; ?>
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                            <p><?php echo htmlspecialchars($listing['description']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>
                            <p><strong>Rating:</strong> ⭐ <?php echo number_format($listing['rating'], 1); ?></p>
                            <p><strong>Price:</strong> Ksh <?php echo number_format($listing['price'], 2); ?>/night</p>
                            <button class="btn btn-primary" onclick="window.location.href='booking.php?listing_id=<?php echo $listing['id']; ?>'">Book Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No listings available at this time.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/navbarroot.php'; ?>
</body>
</html>
