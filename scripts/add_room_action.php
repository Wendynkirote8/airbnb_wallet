<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["admin"])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $location = $_POST["location"];
    $price = $_POST["price"];

    // Handle Image Upload
    $image_name = basename($_FILES["image"]["name"]);
    $target_dir = "../uploads/rooms/";
    $target_file = $target_dir . $image_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO rooms (title, description, location, price_per_night, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $location, $price, $image_name])) {
            header("Location: ../admin/admin_add_room.php?success=1");
        } else {
            echo "Error adding room.";
        }
    } else {
        echo "Image upload failed.";
    }
}
?>
