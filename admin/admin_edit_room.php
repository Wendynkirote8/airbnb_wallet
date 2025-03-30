<?php
session_start();
require_once '../config/db_connect.php';

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if room ID is provided.
if (!isset($_GET['id'])) {
    header("Location: admin_manage_rooms.php");
    exit();
}

$room_id = $_GET['id'];

// Fetch the room details.
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo "Room not found.";
    exit();
}

$error = '';
// Process the form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $capacity = trim($_POST['capacity']);

    if (empty($name) || empty($description) || empty($price) || empty($capacity)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || !is_numeric($capacity)) {
        $error = "Price and Capacity must be numeric values.";
    }

    if (empty($error)) {
        $updatePicture = false;
        $targetDir = "../uploads/";

        // Check if a new picture is uploaded.
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['picture']['type'], $allowedTypes)) {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            } else {
                $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('room_', true) . '.' . $extension;
                $targetFile = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                    $updatePicture = true;
                    // Remove old picture if any.
                    if (!empty($room['picture'])) {
                        $oldPicturePath = $targetDir . $room['picture'];
                        if (file_exists($oldPicturePath)) {
                            unlink($oldPicturePath);
                        }
                    }
                } else {
                    $error = "Error uploading file.";
                }
            }
        }

        // Update the room if no errors so far.
        if (empty($error)) {
            try {
                if ($updatePicture) {
                    $stmt = $pdo->prepare("UPDATE rooms SET name = ?, description = ?, price = ?, capacity = ?, picture = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $capacity, $newFileName, $room_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE rooms SET name = ?, description = ?, price = ?, capacity = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $capacity, $room_id]);
                }
                header("Location: admin_manage_rooms.php?success=RoomUpdated");
                exit();
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Room</title>
  <!-- Bootstrap CSS (light theme) -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Google Font (optional, matching screenshot) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif; 
      background-color: #f5f7fa; /* Light background */
    }
    /* Top navbar */
    .navbar {
      background-color: #fff;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      font-weight: 600;
      color: #000; /* brand text color */
    }
    /* Left sidebar */
    .sidebar {
      background-color: #fff;
      min-height: 100vh;
      border-right: 1px solid #e0e0e0;
      padding: 1rem;
    }
    .sidebar a {
      color: #333;
      display: block;
      margin: 0.5rem 0;
      text-decoration: none;
      font-weight: 500;
    }
    .sidebar a:hover {
      color: #2a2185; /* brand color on hover */
    }
    /* Main content area */
    .main-content {
      padding: 2rem;
      width: 100%;
    }
    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 1px 5px rgba(0,0,0,0.1);
      background: #fff;
    }
    .card-header {
      background: transparent;
      border-bottom: none;
    }
    .card-title {
      font-weight: 600;
      color: #2a2185; /* brand color */
    }
    .btn-brand {
      background-color: #2a2185;
      color: #fff;
      border: none;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }
    .btn-brand:hover {
      background-color: #201a66;
    }
    .error {
      color: red;
      margin-bottom: 1rem;
    }
    .current-picture img {
      max-width: 200px;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <!-- Top Navigation Bar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">WeshPAY</a>
      <div class="ml-auto d-flex align-items-center">
        <!-- Replace with dynamic user info if desired -->
        <span class="mr-3">Welcome, Admin</span>
        <img src="../assets/imgs/default-profile.png" alt="Profile" class="rounded-circle" style="width:40px;height:40px;">
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar">
        <!-- Example nav links (replace with your includes/navbar_admin.php if you like) -->
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_manage_rooms.php">Manage Rooms</a>
      </div>

      <!-- Main Content Area -->
      <div class="col-md-10 main-content">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Edit Room</h3>
          </div>
          <div class="card-body">
            <?php if (!empty($error)): ?>
              <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="name">Room Name:</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  name="name" 
                  value="<?php echo htmlspecialchars($room['name']); ?>" 
                  required
                >
              </div>

              <div class="form-group">
                <label for="description">Description:</label>
                <textarea 
                  name="description" 
                  id="description" 
                  rows="4" 
                  class="form-control" 
                  required
                ><?php echo htmlspecialchars($room['description']); ?></textarea>
              </div>

              <div class="form-group">
                <label for="price">Price:</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="price" 
                  name="price" 
                  value="<?php echo htmlspecialchars($room['price']); ?>" 
                  required
                >
              </div>

              <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input 
                  type="number" 
                  class="form-control" 
                  id="capacity" 
                  name="capacity" 
                  value="<?php echo htmlspecialchars($room['capacity']); ?>" 
                  required
                >
              </div>

              <?php if (!empty($room['picture'])): ?>
                <div class="form-group current-picture">
                  <label>Current Picture:</label><br>
                  <img 
                    src="../uploads/<?php echo htmlspecialchars($room['picture']); ?>" 
                    alt="Room Picture"
                  >
                </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="picture">Change Picture:</label>
                <input 
                  type="file" 
                  class="form-control-file" 
                  id="picture" 
                  name="picture"
                >
              </div>

              <button type="submit" class="btn btn-brand">Update Room</button>
            </form>
          </div>
        </div>
      </div> <!-- End .main-content -->
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
