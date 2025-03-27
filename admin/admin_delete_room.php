<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();
require_once '../config/db_connect.php';

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if room ID is provided via GET.
if (!isset($_GET['id'])) {
    header("Location: admin_manage_rooms.php");
    exit();
}

$room_id = $_GET['id'];

// Fetch the room details from the database.
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    // If the room doesn't exist, you can show an error or redirect.
    echo "Room not found.";
    exit();
}

// OPTIONAL: If you want to delete the uploaded picture from the server, 
// make sure the 'picture' column in your 'rooms' table stores the filename.
if (!empty($room['picture'])) {
    $picturePath = "../uploads/" . $room['picture'];
    if (file_exists($picturePath)) {
        unlink($picturePath); // This removes the file from the server
    }
}

// Delete the room record from the database.
$deleteStmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
$deleteStmt->execute([$room_id]);

// Redirect back to the manage rooms page.
header("Location: admin_manage_rooms.php");
exit();
