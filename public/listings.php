<?php
session_start();
require_once '../config/db_connect.php'; // Ensure $pdo is set up

// Fetch all available rooms
$stmt = $pdo->query("SELECT id, name, description, price, capacity, image FROM rooms ORDER BY created_at DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure user is authenticated and fetch user details
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/customer01.jpg"; // Fallback image
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>weshPAY - Available Rooms</title>
  
  <!-- Unified Styles from your dashboard -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Additional Room Grid Styles -->
  <style>
    .rooms-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    .room-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s;
    }
    .room-card:hover {
      transform: scale(1.02);
    }
    .room-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .room-info {
      padding: 15px;
    }
    .room-info h3 {
      margin-bottom: 10px;
      color: #2a2185;
    }
    .room-info p {
      margin-bottom: 10px;
      font-size: 0.9rem;
      color: #555;
    }
    .room-info .price {
      font-size: 1.1rem;
      font-weight: bold;
      color: #2a2185;
      margin-bottom: 5px;
    }
    .room-info .capacity {
      font-size: 0.9rem;
      color: #777;
    }
    .select-btn {
      display: block;
      text-align: center;
      background-color: #2a2185;
      color: #fff;
      text-decoration: none;
      padding: 10px;
      border-top: 1px solid #ccc;
      transition: background-color 0.3s;
    }
    .select-btn:hover {
      background-color: #1c193f;
    }
  </style>

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
        <li><a href="booking_history.php" class="active"><ion-icon name="receipt-outline"></ion-icon> Booking History</a></li>
        <li><a href="listings.php" class="active"><ion-icon name="bed-outline"></ion-icon> Available Rooms</a></li>
        <li><a href="#"><ion-icon name="chatbubble-outline"></ion-icon> Messages</a></li>
        <li><a href="#"><ion-icon name="help-outline"></ion-icon> Help</a></li>
        <li><a href="#"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="#"><ion-icon name="lock-closed-outline"></ion-icon> Password</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Top Header -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search here" />
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User Profile">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Page Content -->
    <section class="overview">
      <div class="welcome-card">
        <h1>Available Rooms</h1>
        <p>Browse through our available rooms.</p>
      </div>

      <div class="container2">
        <?php if ($rooms && count($rooms) > 0): ?>
          <div class="rooms-grid">
            <?php foreach ($rooms as $room): ?>
              <div class="room-card">
                <?php if (!empty($room['image'])): ?>
                  <img src="../<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                <?php else: ?>
                  <img src="../assets/imgs/default-room.png" alt="Default Room Image">
                <?php endif; ?>
                <div class="room-info">
                  <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                  <p><?php echo htmlspecialchars($room['description']); ?></p>
                  <p class="price">ksh. <?php echo number_format($room['price'], 2); ?></p>
                  <p class="capacity">Capacity: <?php echo htmlspecialchars($room['capacity']); ?></p>
                </div>
                <!-- Link to the booking page -->
                <a href="room_details.php?id=<?php echo $room['id']; ?>" class="select-btn">Select Room</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="text-align: center;">No rooms available at this time.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>
</body>
</html>
