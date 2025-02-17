<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
?>

<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Add a New Room</h2>
    <form action="../scripts/add_room_action.php" method="POST" enctype="multipart/form-data">
        <label>Room Title:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Price Per Night (Ksh):</label>
        <input type="number" name="price" required>

        <label>Room Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Room</button>
    </form>
</div>

<?php include '../includes/navbarroot.php'; ?>
