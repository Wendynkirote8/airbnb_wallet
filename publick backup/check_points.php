<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $points = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0;

    echo "You have $points loyalty points.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
