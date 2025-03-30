<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../config/db_connect.php'; // Ensure $pdo is set up

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
    // New fields:
    $room_rating = trim($_POST['room_rating']); // e.g., numeric value (0-5)
    // Checkbox returns "on" if checked; convert to 1, else 0.
    $room_favourite = isset($_POST['room_favourite']) ? 1 : 0;
    
    if (empty($room_name)) {
        $errors[] = "Room name is required.";
    }
    if (empty($room_price) || !is_numeric($room_price)) {
        $errors[] = "A valid room price is required.";
    }
    if (empty($room_capacity) || !is_numeric($room_capacity)) {
        $errors[] = "A valid room capacity is required.";
    }
    // Optional: Validate rating if provided (allowing 0 to 5)
    if (!empty($room_rating) && (!is_numeric($room_rating) || $room_rating < 0 || $room_rating > 5)) {
        $errors[] = "Room rating must be a number between 0 and 5.";
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
                // Store relative path (prepend "rooms/" so that display code can prepend "../uploads/")
                $imagePath = "uploads/rooms/" . $newFileName;
            } else {
                $errors[] = "Failed to upload the image.";
            }
        }
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO rooms (name, description, price, capacity, image, rating, favourite)
                VALUES (:name, :description, :price, :capacity, :image, :rating, :favourite)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':name', $room_name);
        $stmt->bindParam(':description', $room_description);
        $stmt->bindParam(':price', $room_price);
        $stmt->bindParam(':capacity', $room_capacity);
        $stmt->bindParam(':image', $imagePath);
        // If rating is empty, store null (or you can default to 0)
        $ratingValue = !empty($room_rating) ? $room_rating : null;
        $stmt->bindParam(':rating', $ratingValue);
        $stmt->bindParam(':favourite', $room_favourite, PDO::PARAM_INT);

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
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    /* Overall container for the page */
    .container {
      display: flex;
      min-height: 100vh;
    }
    
    /* Main content area */
    .main {
      flex-grow: 1;
      padding: 20px;
      background-color: #f4f4f4;
      overflow-y: auto;
    }
    /* Topbar styles */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #fff;
      padding: 10px 20px;
      border-bottom: 1px solid #ccc;
    }
    
    /* Form container styles */
    .form-container {
      max-width: 800px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 8px;
      padding: 2rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }
    .form-container h2 {
      margin-bottom: 1.5rem;
      color: #2a2185;
      text-align: center;
    }
    .custom-alert {
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 4px;
      font-weight: 500;
      line-height: 1.3;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #842029;
    }
    .alert-success {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    /* Form fields styling */
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #2a2185;
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      border-color: #2a2185;
      outline: none;
    }
    /* Side-by-side layout using Flexbox */
    .form-row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .form-col {
      flex: 1;
      min-width: 200px;
    }
    /* Button styles */
    .custom-btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 4px;
      background-color: #2a2185;
      color: #fff;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .custom-btn:hover {
      background-color: #1c193f;
    }
    /* Responsive tweaks */
    @media (max-width: 600px) {
      .form-row {
        flex-direction: column;
      }
    }
  </style>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <div class="container">
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>
    <div class="main">
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

      <div class="content">
        <div class="form-container">
          <h2>Add New Room</h2>
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
          <form action="admin_add_room.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="room_name">Room Name</label>
              <input type="text" id="room_name" name="room_name" required>
            </div>
            <div class="form-group">
              <label for="room_description">Room Description</label>
              <textarea id="room_description" name="room_description" rows="3" required></textarea>
            </div>
            <!-- Row for Price and Capacity -->
            <div class="form-row">
              <div class="form-col">
                <label for="room_price">Room Price</label>
                <input type="number" step="0.01" id="room_price" name="room_price" required>
              </div>
              <div class="form-col">
                <label for="room_capacity">Room Capacity</label>
                <input type="number" id="room_capacity" name="room_capacity" required>
              </div>
            </div>
            <!-- Row for Rating and Favourite -->
            <div class="form-row">
              <div class="form-col">
                <label for="room_rating">Room Rating (0 to 5)</label>
                <input type="number" id="room_rating" name="room_rating" step="0.1" min="0" max="5" placeholder="e.g., 4.5">
              </div>
              <div class="form-col" style="display: flex; align-items: center; gap: 10px; margin-top: 1.7rem;">
                <input type="checkbox" id="room_favourite" name="room_favourite">
                <label for="room_favourite">Mark as Favourite</label>
              </div>
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
  <script src="../assets/js/main.js"></script>
</body>
</html>
