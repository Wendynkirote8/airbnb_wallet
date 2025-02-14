<?php
session_start();
require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

// Fetch user's details
$stmt = $pdo->prepare("SELECT full_name, email, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch user's loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$loyalty = $stmt->fetch(PDO::FETCH_ASSOC);

$points = $loyalty ? $loyalty["points"] : 0;
?>

<?php include '../includes/navbar.php'; ?>

<div class="update-profile-container">
    <h2>Update Profile</h2>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="file" name="profile_picture">
        <button type="submit">Save Changes</button>
    </form>
</div>

<?php include '../includes/navbarroot.php'; ?>
