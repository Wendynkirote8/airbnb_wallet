<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../admin/config/db_connect.php'; // This file should create a PDO instance named $pdo

// Ensure only admins can access this page
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$success = "";
$imagePath = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and validate form inputs
    $room_name = trim($_POST['room_name']);
    $room_description = trim($_POST['room_description']);
    $room_price = trim($_POST['room_price']);
    $room_capacity = trim($_POST['room_capacity']);

    if (empty($room_name)) {
        $errors[] = "Room name is required.";
    }
    if (empty($room_price) || !is_numeric($room_price)) {
        $errors[] = "A valid room price is required.";
    }
    if (empty($room_capacity) || !is_numeric($room_capacity)) {
        $errors[] = "A valid room capacity is required.";
    }

    // Check if an image was uploaded
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] != UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['room_image']['type'];
        $fileSize = $_FILES['room_image']['size'];
        $maxSize = 5 * 1024 * 1024; // 5MB max

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF files are allowed.";
        }
        if ($fileSize > $maxSize) {
            $errors[] = "Image size must be less than 5MB.";
        }

        // If no errors with image, move the uploaded file
        if (empty($errors)) {
            $uploadDir = __DIR__ . "/../uploads/rooms/";
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $errors[] = "Failed to create uploads directory.";
                }
            }
            $fileExtension = pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid("room_", true) . "." . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['room_image']['tmp_name'], $destination)) {
                $imagePath = "uploads/rooms/" . $newFileName;
            } else {
                $errors[] = "Failed to upload the image.";
            }
        }
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO rooms (name, description, price, capacity, image)
                VALUES (:name, :description, :price, :capacity, :image)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':name', $room_name);
        $stmt->bindParam(':description', $room_description);
        $stmt->bindParam(':price', $room_price);
        $stmt->bindParam(':capacity', $room_capacity);
        $stmt->bindParam(':image', $imagePath);

        if ($stmt->execute()) {
            $success = "Room added successfully!";
        } else {
            $errors[] = "Something went wrong while adding the room.";
        }
    }
}

// Fetch admin details
$stmt = $pdo->prepare("SELECT username, email FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION["admin_id"]]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $admin['username'] ?? 'Admin';
$email = $admin['email'] ?? 'admin@example.com';
$profile_picture = "../assets/imgs/default-profile.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Room - Admin Dashboard</title>

  <!-- Main Theme CSS (Optional, if you have one) -->
  <link rel="stylesheet" href="../assets/css/style.css">

  <!-- Internal CSS to style the form -->
  <style>
    /* Center the entire content area */
    .main .content {
      padding: 20px;
    }

    /* Centered Card Container */
    .form-container {
      max-width: 600px;
      margin: 2rem auto;          /* Center horizontally */
      background: #fff;
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }
    .form-container h2 {
      margin-bottom: 1rem;
      color: #2a2185; /* var(--blue) fallback */
    }

    /* Alert messages */
    .custom-alert {
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 4px;
      font-weight: 500;
      line-height: 1.3;
    }
    .custom-alert.alert-error {
      background-color: #f8d7da;
      color: #842029;
    }
    .custom-alert.alert-success {
      background-color: #d1e7dd;
      color: #0f5132;
    }

    /* Form fields */
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.4rem;
      font-weight: 600;
      color: #2a2185; /* var(--blue) fallback */
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group textarea {
      width: 100%;
      padding: 0.7rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    .form-group textarea {
      resize: vertical;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      border-color: #2a2185; /* var(--blue) fallback */
      outline: none;
    }

    /* Submit button */
    .custom-btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      margin-top: 0.5rem;
      border: none;
      border-radius: 4px;
      background-color: #2a2185; /* var(--blue) fallback */
      color: #fff;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .custom-btn:hover {
      background-color: #1c193f; /* var(--blue2) fallback */
    }
  </style>

  <!-- Ionicons (for icons) -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <ul>
        <li>
          <a href="#">
            <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
            <span class="title">Wesh Pay</span>
          </a>
        </li>
        <li>
          <a href="admin_dashboard.php">
            <span class="icon"><ion-icon name="grid-outline"></ion-icon></span>
            <span class="title">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="admin_add_room.php" class="active-link">
            <span class="icon"><ion-icon name="bed-outline"></ion-icon></span>
            <span class="title">Add Room</span>
          </a>
        </li>
        <li>
          <a href="admin_manage_rooms.php">
            <span class="icon"><ion-icon name="list-outline"></ion-icon></span>
            <span class="title">Manage Rooms</span>
          </a>
        </li>
        <li>
          <a href="admin_manage_users.php">
            <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
            <span class="title">Manage Users</span>
          </a>
        </li>
        <li>
          <a href="admin_change_password.php">
            <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
            <span class="title">Change Password</span>
          </a>
        </li>
        <li>
          <a href="admin_transactions.php">
            <span class="icon"><ion-icon name="receipt-outline"></ion-icon></span>
            <span class="title">Transactions</span>
          </a>
        </li>
        <li>
          <a href="../public/logout.php">
            <span class="icon"><ion-icon name="log-out-outline"></ion-icon></span>
            <span class="title">Sign Out</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="toggle">
          <ion-icon name="menu-outline"></ion-icon>
        </div>
        <div class="search">
          <label>
            <input type="text" placeholder="Search here">
            <ion-icon name="search-outline"></ion-icon>
          </label>
        </div>
        <div class="user" onclick="toggleProfileDropdown()">
          <img src="<?php echo $profile_picture; ?>" alt="Admin Profile">
        </div>
        <!-- Profile Dropdown -->
        <div id="profileDropdown" class="dropdown">
          <div class="dropdown-content">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <p class="user-name"><strong><?php echo htmlspecialchars($username); ?></strong></p>
            <p class="user-email"><?php echo htmlspecialchars($email); ?></p>
            <button onclick="window.location.href='admin_edit_profile.php'">Edit Profile</button>
            <button onclick="window.location.href='../public/logout.php'">Logout</button>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <div class="content">
        <!-- Centered Form Container -->
        <div class="form-container">
          <h2>Add New Room</h2>

          <!-- Error or Success Alerts -->
          <?php if (!empty($errors)): ?>
            <div class="custom-alert alert-error">
              <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($success)): ?>
            <div class="custom-alert alert-success">
              <p><?php echo htmlspecialchars($success); ?></p>
            </div>
          <?php endif; ?>

          <!-- Add Room Form -->
          <form action="admin_add_room.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="room_name">Room Name</label>
              <input type="text" id="room_name" name="room_name" required>
            </div>
            <div class="form-group">
              <label for="room_description">Room Description</label>
              <textarea id="room_description" name="room_description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="room_price">Room Price</label>
              <input type="number" step="0.01" id="room_price" name="room_price" required>
            </div>
            <div class="form-group">
              <label for="room_capacity">Room Capacity</label>
              <input type="number" id="room_capacity" name="room_capacity" required>
            </div>
            <div class="form-group">
              <label for="room_image">Room Image</label>
              <input type="file" id="room_image" name="room_image" accept="image/*">
            </div>
            <button type="submit" class="custom-btn">Add Room</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleProfileDropdown() {
      document.getElementById("profileDropdown").classList.toggle("show");
    }
    window.onclick = function(event) {
      if (!event.target.closest(".user")) {
        document.getElementById("profileDropdown").classList.remove("show");
      }
    }
  </script>
  <!-- If you have a main.js for sidebar toggles, keep this reference -->
  <script src="../assets/js/main.js"></script>
</body>
</html>
