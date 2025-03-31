<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Secure hash
    $role = "guest";  // Default role

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $phone, $password, $role]);
        echo "Registration successful!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
