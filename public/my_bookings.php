<?php
// my_bookings_grid.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];

// Fetch user details (for header display)
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"])
    ? "../uploads/" . $user["profile_picture"]
    : "../assets/imgs/default-user.png";

// Fetch user's bookings along with room details including image
try {
    $stmt = $pdo->prepare("
    SELECT b.id AS booking_id, r.name AS room_name, r.image AS room_image, 
           b.days, b.total_cost, b.booking_date, b.status 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Bookings - WeshPAY</title>
  <!-- Include your existing dashboard stylesheet -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">
  <style>
    /* Basic styling for the grid layout */
    .bookings-grid-section {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .bookings-grid-section h2 {
      margin-top: 0;
      font-size: 1.8rem;
      color: #2a2185;
      border-bottom: 2px solid #2a2185;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .bookings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .booking-card {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      display: flex;
      flex-direction: column;
    }
    .booking-card:hover {
      transform: translateY(-3px);
    }
    .booking-image img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      display: block;
    }
    .booking-details {
      padding: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .booking-details h3 {
      margin: 0 0 10px;
      font-size: 1.2rem;
      color: #2a2185;
    }
    .booking-details p {
      margin: 5px 0;
      font-size: 0.9rem;
      color: #666;
    }
    .booking-details .booking-price {
      font-weight: bold;
      color: #2a2185;
      margin-top: 10px;
    }
    .booking-details a {
      display: inline-block;
      margin-top: 15px;
      padding: 8px 12px;
      background: #2a2185;
      color: #fff;
      border-radius: 6px;
      transition: background 0.3s;
      text-align: center;
      font-size: 0.9rem;
    }
    .booking-details a:hover {
      background: #1c193f;
    }
  </style>
  <!-- Ionicons for icons if needed -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <!-- Sidebar (Retain your existing navigation) -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>WeshPAY</h2>
    </div>
    <?php include '../includes/navbar.php'; ?>
  </aside>

  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Top Header -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search here">
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User Profile">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Bookings Grid Section -->
    <section class="overview">
      <div class="bookings-grid-section">
        <h2>My Booked Rooms</h2>
        <?php if (!empty($bookings)): ?>
          <div class="bookings-grid">
            <?php foreach ($bookings as $booking): ?>
              <div class="booking-card">
                <?php if (!empty($booking['room_image'])): ?>
                  <div class="booking-image">
                  <img src="../<?php echo htmlspecialchars($booking['room_image']); ?>" alt="Room Image">

                  </div>
                <?php endif; ?>
                <div class="booking-details">
                  <h3><?php echo htmlspecialchars($booking['room_name']); ?></h3>
                  <p><strong>Days:</strong> <?php echo htmlspecialchars($booking['days']); ?></p>
                  <p><strong>Total:</strong> ksh. <?php echo number_format($booking['total_cost'], 2); ?></p>
                  <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                  <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($booking['status'])); ?></p>
                  <a href="booking_details.php?id=<?php echo $booking['booking_id']; ?>">View Details</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="text-align: center;">You have not booked any rooms yet.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>
</body>
</html>
