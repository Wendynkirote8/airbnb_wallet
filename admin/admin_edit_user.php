<?php
session_start();

// Turn on error logging to file instead of displaying on the page.
// This helps keep the layout clean if there's a warning/error.
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db_connect.php'; // Ensure this file sets up $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided.
if (!isset($_GET['id'])) {
    header("Location: admin_manage_users.php");
    exit();
}

$user_id = $_GET['id'];

// Fetch user details.
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // If no user found, show a simple error and exit.
    echo "User not found.";
    exit();
}

$error = '';

// Process the form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $role      = trim($_POST['role']);

    if (empty($full_name) || empty($email) || empty($role)) {
        $error = "Full name, email, and role are required.";
    }

    if (empty($error)) {
        $updatePicture = false;

        // Use absolute path so there's no confusion about ../
        $targetDir = __DIR__ . "/../uploads/users/";

        // Ensure the target directory exists (and create it if not).
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $error = "Failed to create directory for uploads.";
            }
        }

        // Proceed if no error after directory check.
        if (empty($error)) {
            // Check if a new profile picture is uploaded.
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
                    $error = "Only JPG, PNG, and GIF files are allowed for profile pictures.";
                } else {
                    // Generate a unique file name and move the file.
                    $extension   = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                    $newFileName = uniqid('user_', true) . '.' . $extension;
                    $targetFile  = $targetDir . $newFileName;

                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                        $updatePicture = true;
                        // Remove old picture if one exists.
                        if (!empty($user['profile_picture'])) {
                            $oldPicturePath = $targetDir . $user['profile_picture'];
                            if (file_exists($oldPicturePath)) {
                                unlink($oldPicturePath);
                            }
                        }
                    } else {
                        $error = "Error uploading profile picture.";
                    }
                }
            }
        }

        // Update the user record if still no errors.
        if (empty($error)) {
            try {
                if ($updatePicture) {
                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET full_name = ?, email = ?, phone = ?, role = ?, profile_picture = ?
                        WHERE user_id = ?
                    ");
                    $stmt->execute([$full_name, $email, $phone, $role, $newFileName, $user_id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE users
                        SET full_name = ?, email = ?, phone = ?, role = ?
                        WHERE user_id = ?
                    ");
                    $stmt->execute([$full_name, $email, $phone, $role, $user_id]);
                }
                header("Location: admin_manage_users.php?success=UserUpdated");
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
  <title>Edit User - Admin Dashboard</title>

  <!-- Bootstrap CSS (CDN) -->
  <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
  />

  <!-- Ionicons (for icons) -->
  <script
    type="module"
    src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js">
  </script>
  <script
    nomodule
    src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js">
  </script>

  <!-- Custom Admin Style (optional) -->
  <link rel="stylesheet" href="../assets/css/admin_style.css">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
    }
    .container-fluid {
      min-height: 100vh;
      display: flex;
      flex-wrap: nowrap;
      padding: 0;
    }
    .navigation {
      width: 250px;
      background-color: #fff;
      border-right: 1px solid #e0e0e0;
      padding: 1rem;
    }
    .main-content {
      flex: 1;
      padding: 1rem 2rem;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .topbar .search input {
      padding: 8px;
      width: 200px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    .topbar .user img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    .error {
      color: #ff0000;
      margin-bottom: 1rem;
    }
    .card {
      margin-top: 20px;
    }
    .btn-brand {
      background-color: #2a2185;
      color: #fff;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-brand:hover {
      background-color: #201a66;
    }
    .current-picture img {
      max-width: 150px;
      border-radius: 4px;
      margin-top: 10px;
      display: block;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>
    <!-- Main Content Area -->
    <div class="main-content">
      <!-- Topbar -->
      <div class="topbar">
        <div class="search">
          <input type="text" placeholder="Search here">
        </div>
        <div class="user">
          <img src="../assets/imgs/default-profile.png" alt="Admin Profile">
        </div>
      </div>

      <!-- Page Content -->
      <div class="container">
        <h2 class="mt-4">Edit User</h2>

        <!-- Error Alert (Bootstrap) -->
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="card-body">
            <form method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="full_name">Full Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="full_name"
                  name="full_name"
                  value="<?php echo htmlspecialchars($user['full_name']); ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  value="<?php echo htmlspecialchars($user['email']); ?>"
                  required
                />
              </div>

              <div class="form-group">
                <label for="phone">Phone</label>
                <input
                  type="text"
                  class="form-control"
                  id="phone"
                  name="phone"
                  value="<?php echo htmlspecialchars($user['phone']); ?>"
                />
              </div>

              <div class="form-group">
                <label for="role">Role</label>
                <select
                  class="form-control"
                  id="role"
                  name="role"
                  required
                >
                  <option value="guest" <?php echo ($user['role'] === 'guest') ? 'selected' : ''; ?>>Guest</option>
                  <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                  <!-- Add more roles if needed -->
                </select>
              </div>

              <?php if (!empty($user['profile_picture'])): ?>
                <div class="form-group current-picture">
                  <label>Current Profile Picture:</label>
                  <img
                    src="../uploads/users/<?php echo htmlspecialchars($user['profile_picture']); ?>"
                    alt="Profile Picture"
                  />
                </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="profile_picture">Change Profile Picture</label>
                <input
                  type="file"
                  class="form-control-file"
                  id="profile_picture"
                  name="profile_picture"
                />
              </div>

              <button type="submit" class="btn btn-brand">
                Update User
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (CDN) -->
  <script
    src="https://code.jquery.com/jquery-3.5.1.slim.min.js">
  </script>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
