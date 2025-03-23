<?php
session_start();
require '../admin/config/db_connect.php';

// Check if the admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $location = $_POST["location"];
    $amenities = $_POST["amenities"];
    
    // Handle image upload
    $image_url = "";
    if (!empty($_FILES["image"]["name"])) {
        $image_name = time() . "_" . $_FILES["image"]["name"];
        $target_path = "../uploads/" . $image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            $image_url = "uploads/" . $image_name;
        }
    }

    // Insert listing into the database
    $stmt = $pdo->prepare("INSERT INTO listings (title, description, price, location, amenities, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $price, $location, $amenities, $image_url]);

    header("Location: listings.php");
    exit();
}
?>

<?php include '../includes/navbar.php'; ?>

<h2>Add a New Listing</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Listing Title" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" name="price" placeholder="Price per Night (Ksh)" required>
    <input type="text" name="location" placeholder="Location" required>
    <input type="text" name="amenities" placeholder="Amenities (comma-separated)" required>
    <input type="file" name="image" required>
    <button type="submit">Add Listing</button>
</form>

<?php include '../includes/navbarroot.php'; ?>
