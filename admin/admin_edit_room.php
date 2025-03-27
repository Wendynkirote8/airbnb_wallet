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
    // Sanitize and validate inputs.
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $capacity = trim($_POST['capacity']);

    if (empty($name) || empty($description) || empty($price) || empty($capacity)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || !is_numeric($capacity)) {
        $error = "Price and Capacity must be numeric values.";
    }

    // Only continue if no validation error.
    if (empty($error)) {
        // Initialize picture update flag.
        $updatePicture = false;

        // Check if a new picture is uploaded.
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            // Validate file type.
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['picture']['type'], $allowedTypes)) {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            } else {
                // Rename the file to avoid collisions.
                $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('room_', true) . '.' . $extension;
                $targetDir = "../uploads/";
                $targetFile = $targetDir . $newFileName;

                // Move the uploaded file.
                if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                    $updatePicture = true;
                    // Optionally, delete the old picture if it exists.
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

        // If no error, update the room in the database.
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
    <style>
      body {
        font-family: "Ubuntu", sans-serif;
        background: #f5f5f5;
        padding: 20px;
      }
      .container {
        display: flex;
      }
      .navigation {
        width: 300px;
      }
      .main {
        flex: 1;
      }
      form {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        max-width: 600px;
        margin: 0 auto;
      }
      label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
      input[type="text"],
      input[type="number"],
      textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      input[type="file"] {
        margin-bottom: 15px;
      }
      input[type="submit"] {
        padding: 10px 15px;
        background: #2a2185;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
      }
      .error {
        color: red;
        margin-bottom: 15px;
      }
      .current-picture {
        margin-bottom: 15px;
      }
      .current-picture img {
        max-width: 200px;
      }
    </style>
</head>
<body>
    <div class="container">
      <!-- Navigation Sidebar -->
      <div class="navigation">
         <?php include '../includes/navbar_admin.php'; ?>
      </div>
      <div class="main">
        <h2>Edit Room</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Room Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required>
            
            <label>Description:</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($room['description']); ?></textarea>
            
            <label>Price:</label>
            <input type="text" name="price" value="<?php echo htmlspecialchars($room['price']); ?>" required>
            
            <label>Capacity:</label>
            <input type="number" name="capacity" value="<?php echo htmlspecialchars($room['capacity']); ?>" required>
            
            <?php if (!empty($room['picture'])): ?>
              <div class="current-picture">
                <label>Current Picture:</label><br>
                <img src="../uploads/<?php echo htmlspecialchars($room['picture']); ?>" alt="Room Picture">
              </div>
            <?php endif; ?>
            
            <label>Change Picture:</label>
            <input type="file" name="picture">
            
            <input type="submit" value="Update Room">
        </form>
      </div>
    </div>
</body>
</html>
