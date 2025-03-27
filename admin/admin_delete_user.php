<?php
session_start();
require_once '../config/db_connect.php'; // This file creates a PDO instance named $pdo

// Ensure only admins can access this page
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if a user ID is provided via GET
if (!isset($_GET['id'])) {
    header("Location: admin_manage_users.php");
    exit();
}

$user_id = $_GET['id'];

try {
    // 1. Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // If the user does not exist, redirect or show a message
        header("Location: admin_manage_users.php?error=UserNotFound");
        exit();
    }

    // 2. (Optional) Delete profile picture file if it exists
    //    Adjust the path if you store images differently.
    if (!empty($user['profile_picture'])) {
        $picturePath = "../" . $user['profile_picture'];
        if (file_exists($picturePath) && is_writable($picturePath)) {
            unlink($picturePath);
        }
    }

    // 3. Delete the user from the database
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteStmt->execute([$user_id]);

    // 4. Redirect back to Manage Users
    header("Location: admin_manage_users.php?success=UserDeleted");
    exit();

} catch (PDOException $e) {
    // If there is a foreign key constraint or another DB error, you'll see it here
    echo "Error deleting user: " . $e->getMessage();
    exit();
}
