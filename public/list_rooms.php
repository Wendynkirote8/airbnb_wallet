<?php
require '../config/db_connect.php';

$stmt = $pdo->query("SELECT * FROM rooms ORDER BY created_at DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/navbar.php'; ?>

<div class="rooms-container">
    <?php foreach ($rooms as $room): ?>
        <div class="room-card">
            <img src="../uploads/<?php echo htmlspecialchars($room['image']); ?>" alt="Room Image">
            <h3><?php echo htmlspecialchars($room['title']); ?></h3>
            <p><?php echo htmlspecialchars($room['location']); ?></p>
            <p><strong>Ksh <?php echo number_format($room['price_per_night'], 2); ?> per night</strong></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/navbarroot.php'; ?>
