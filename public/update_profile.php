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

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    
    // Handle profile picture upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Move file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update user data with profile picture
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $target_file, $user_id]);
        } else {
            die("File upload failed.");
        }
    } else {
        // Update user data without profile picture
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $email, $user_id]);
    }

    // Redirect after update
    header("Location: profile.php?success=1");
    exit();
}
?>

<?php include '../includes/navbar.php'; ?>

<div class="update-profile-container">
    <h2>Update Profile</h2>
    <?php if (isset($_GET['success'])) echo "<p style='color: green;'>Profile updated successfully!</p>"; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="file" name="profile_picture">
        <button type="submit">Save Changes</button>
    </form>
</div>

<?php include '../includes/navbarroot.php'; ?>
