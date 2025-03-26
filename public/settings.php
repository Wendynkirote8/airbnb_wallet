<?php
session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];

// Initialize feedback messages
$successMessage = $errorMessage = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and trim form fields
    $full_name_input = trim($_POST['full_name']);
    $email_input = trim($_POST['email']);
    $phone_input = trim($_POST['phone']);
    
    // Notification preferences (checkboxes)
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
    
    // Password fields
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate new password if provided
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errorMessage = "Passwords do not match.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // --- Handle Profile Picture Upload ---
            $profilePicturePath = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
                // Allowed file types
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                $fileInfo = pathinfo($_FILES['profile_picture']['name']);
                $extension = strtolower($fileInfo['extension']);
                if (!in_array($extension, $allowedExts)) {
                    throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
                }
                // Optional: Check file size (e.g., max 2MB)
                if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
                    throw new Exception("File is too large. Maximum allowed size is 2MB.");
                }
                // Create a unique file name and move the uploaded file
                $newFileName = uniqid('profile_', true) . '.' . $extension;
                $destination = "../uploads/" . $newFileName;
                if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                    throw new Exception("Failed to upload profile picture.");
                }
                $profilePicturePath = $newFileName; // Save the new file name
            }
            
            // --- Update Users Table (Profile Info) ---
            if ($profilePicturePath) {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, profile_picture = ? WHERE user_id = ?");
                $stmt->execute([$full_name_input, $email_input, $phone_input, $profilePicturePath, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?");
                $stmt->execute([$full_name_input, $email_input, $phone_input, $user_id]);
            }
            
            // --- Update or Insert Notification Preferences ---
            $stmt = $pdo->prepare("SELECT user_id FROM user_settings WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $existingSettings = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingSettings) {
                $stmt = $pdo->prepare("UPDATE user_settings SET email_notifications = ?, sms_notifications = ? WHERE user_id = ?");
                $stmt->execute([$email_notifications, $sms_notifications, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, email_notifications, sms_notifications) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $email_notifications, $sms_notifications]);
            }
            
            // --- Update Password if Provided ---
            if (!empty($new_password)) {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $stmt->execute([$hashedPassword, $user_id]);
            }
            
            $pdo->commit();
            $successMessage = "Settings updated successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}

// Fetch updated user details for pre-filling the form
$stmt = $pdo->prepare("SELECT full_name, profile_picture, email, phone FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("User not found.");
}
$full_name = $user["full_name"];
$email = $user["email"];
$phone = $user["phone"];
$profile_picture = !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/default-user.png";

// Fetch user settings for notifications
$stmt = $pdo->prepare("SELECT email_notifications, sms_notifications FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$email_notifications = $settings ? $settings["email_notifications"] : 0;
$sms_notifications = $settings ? $settings["sms_notifications"] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>weshPAY - Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Unified Styles -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">
  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <style>
    .settings-form {
      max-width: 600px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .settings-form label {
      display: block;
      margin: 10px 0 5px;
    }
    .settings-form input[type="text"],
    .settings-form input[type="email"],
    .settings-form input[type="password"],
    .settings-form input[type="file"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
    }
    .settings-form button {
      background-color: #2a2185;
      color: #fff;
      padding: 10px 20px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
    }
    .settings-form button:hover {
      background-color: #1c193f;
    }
    .message {
      max-width: 600px;
      margin: 20px auto;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
    }
    .message.success {
      background-color: #d4edda;
      color: #155724;
    }
    .message.error {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a></li>
        <li><a href="listings.php"><ion-icon name="bed-outline"></ion-icon> Available AirBNB</a></li>
        <li><a href="booking_history.php"><ion-icon name="receipt-outline"></ion-icon> Booking History</a></li>
        <li><a href="deposit.php"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <li><a href="redeem_points.php"><ion-icon name="gift-outline"></ion-icon> Redeem Points</a></li>
        <li><a href="messages.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Messages</a></li>
        <li><a href="settings.php" class="active"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search...">
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Settings Page Content -->
    <section class="overview">
      <div class="welcome-card">
        <h1>Account Settings</h1>
        <p>Update your profile and preferences here.</p>
      </div>

      <!-- Display feedback messages -->
      <?php if (!empty($successMessage)): ?>
        <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php elseif (!empty($errorMessage)): ?>
        <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
      <?php endif; ?>

      <div class="settings-form">
        <form method="POST" action="" enctype="multipart/form-data">
          <label for="profile_picture">Profile Picture</label>
          <input type="file" name="profile_picture" id="profile_picture">
          
          <label for="full_name">Full Name</label>
          <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
          
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
          
          <label for="phone">Phone</label>
          <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
          
          <h3>Notification Preferences</h3>
          <label>
            <input type="checkbox" name="email_notifications" <?php echo ($email_notifications ? 'checked' : ''); ?>>
            Email Notifications
          </label>
          <label>
            <input type="checkbox" name="sms_notifications" <?php echo ($sms_notifications ? 'checked' : ''); ?>>
            SMS Notifications
          </label>
          
          <h3>Change Password</h3>
          <label for="new_password">New Password</label>
          <input type="password" name="new_password" id="new_password">
          
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" name="confirm_password" id="confirm_password">
          
          <button type="submit">Save Settings</button>
        </form>
      </div>
    </section>
  </div>

  <script src="../assets/js/dashboard_new.js"></script>
</body>
</html>
