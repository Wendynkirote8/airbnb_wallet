<?php
session_start();
require '../admin/config/db_connect.php'; // This should set up a PDO instance in $pdo

// Check if the admin is logged in.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $location = trim($_POST["location"]);
    $amenities = trim($_POST["amenities"]);
    
    // Initialize image URL.
    $image_url = "";
    if (!empty($_FILES["image"]["name"])) {
        // Generate a unique file name.
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_path = "../uploads/" . $image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            $image_url = "uploads/" . $image_name;
        }
    }
    
    // Insert listing into the database.
    $stmt = $pdo->prepare("INSERT INTO listings (title, description, price, location, amenities, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $price, $location, $amenities, $image_url]);

    header("Location: listings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add a New Listing - WeshPAY</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Google Font: Inter or Ubuntu (choose your preference) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --blue: #2a2185;
      --blue2: #1f1a65;
      --white: #ffffff;
      --gray: #f5f7fa;
    }
    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--gray);
    }
    /* Top Navbar */
    .navbar {
      background-color: var(--white);
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      font-weight: 600;
      color: #000;
    }
    /* Sidebar styling */
    .sidebar {
      background-color: var(--white);
      min-height: 100vh;
      border-right: 1px solid #e0e0e0;
      padding: 1rem;
    }
    .sidebar a {
      color: #333;
      display: block;
      margin: 0.75rem 0;
      text-decoration: none;
      font-weight: 500;
    }
    .sidebar a:hover {
      color: var(--blue);
    }
    /* Main content area */
    .main-content {
      padding: 2rem;
    }
    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      background-color: var(--white);
    }
    .card-header {
      background-color: transparent;
      border-bottom: none;
      padding-bottom: 0;
    }
    .card-title {
      font-weight: 600;
      color: var(--blue);
    }
    .btn-brand {
      background-color: var(--blue);
      color: var(--white);
      border: none;
      border-radius: 4px;
      padding: 10px 20px;
      transition: background-color 0.3s ease;
    }
    .btn-brand:hover {
      background-color: var(--blue2);
    }
  </style>
</head>
<body>
  <!-- Top Navigation Bar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">WeshPAY</a>
      <div class="ml-auto d-flex align-items-center">
        <!-- Optional user info -->
        <span class="mr-3">Welcome, Admin</span>
        <img src="../assets/imgs/default-profile.png" alt="Profile" class="rounded-circle" style="width:40px;height:40px;">
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar">
        <?php include '../includes/navbar_admin.php'; ?>
      </div>
      <!-- Main Content Area -->
      <div class="col-md-10 main-content">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Add a New Listing</h3>
          </div>
          <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label for="title">Listing Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Listing Title" required>
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Description" required></textarea>
              </div>
              <div class="form-group">
                <label for="price">Price per Night (Ksh)</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Price per Night" required>
              </div>
              <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" placeholder="Location" required>
              </div>
              <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <input type="text" name="amenities" id="amenities" class="form-control" placeholder="Amenities" required>
              </div>
              <div class="form-group">
                <label for="image">Listing Image</label>
                <input type="file" name="image" id="image" class="form-control-file" required>
              </div>
              <button type="submit" class="btn btn-brand">Add Listing</button>
            </form>
          </div>
        </div>
      </div> <!-- End Main Content -->
    </div>
  </div>

  <!-- Optional JavaScript & Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
