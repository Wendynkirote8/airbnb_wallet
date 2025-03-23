<?php
require '../config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>E-Wallet System</title>
</head>
<body>
    <h1>Welcome to the E-Wallet System</h1>
    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="dashboard.php">Go to Dashboard</a>
    <?php else: ?>
        <a href="register.php">Register</a> | <a href="login.php">Login</a>
    <?php endif; ?>
</body>
</html>
