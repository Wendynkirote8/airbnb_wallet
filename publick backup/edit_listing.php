<?php
session_start();
require '../config/db_connect.php';

// Get listing details
$listing_id = $_GET["id"] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
$stmt->execute([$listing_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    echo "Listing not found!";
    exit();
}
?>

<h2>Update Listing Image</h2>
<form action="update_image.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
    
    <label>Current Image:</label><br>
    <img src="<?php echo htmlspecialchars($listing['image_url']); ?>" width="200"><br><br>
    
    <label>Upload New Image:</label>
    <input type="file" name="listing_image" required><br><br>

    <button type="submit">Update Image</button>
</form>
