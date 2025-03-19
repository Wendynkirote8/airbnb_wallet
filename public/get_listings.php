<?php
require '../config/db_connect.php';

$stmt = $pdo->query("SELECT * FROM listings ORDER BY rating DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($listings);
?>
