<?php
session_start();
require '../config/db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["listing_image"])) {
    $listing_id = $_POST["listing_id"];
    $upload_dir = "../uploads/";
    
    // Get file info
    $image_name = basename($_FILES["listing_image"]["name"]);
    $target_file = $upload_dir . $image_name;
    
    // Validate file type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($imageFileType, $allowed_types)) {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
        exit();
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["listing_image"]["tmp_name"], $target_file)) {
        // Update database with new image path
        $stmt = $pdo->prepare("UPDATE listings SET image_url = ? WHERE id = ?");
        $stmt->execute([$target_file, $listing_id]);
        
        echo "Image updated successfully!";
    } else {
        echo "Error uploading file.";
    }
}
?>
