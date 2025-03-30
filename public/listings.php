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
      margin-top: 20px;
    }
    .room-card {
      position: relative; /* for the heart icon */
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
    /* Heart (favorite) icon in the top-right corner */
    .heart-icon {
      position: absolute;
      top: 10px;
      right: 10px;
      color: #999;
      font-size: 1.4rem;
      cursor: pointer;
      transition: color 0.3s;
    }
    .heart-icon:hover {
      color: #e74c3c; /* red on hover */
    }
    .room-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .room-info {
      padding: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .badge {
      background-color: #2a2185;
      color: #fff;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .room-info h3 {
      margin: 6px 0;
      font-size: 1.1rem;
      color: #2a2185;
    }
    .hosted {
      color: #555;
      font-size: 0.85rem;
      margin-bottom: 5px;
    }
    .rating {
      display: flex;
      align-items: center;
      gap: 4px;
      color: #ff9900;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 5px;
    }
    .rating ion-icon {
      font-size: 1rem;
    }
    .price-info {
      font-size: 1rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }
    .room-info .capacity {
      font-size: 0.85rem;
      color: #777;
    }
    .select-btn {
      display: block;
      margin-top: 8px;
      text-align: center;
      background-color: #2a2185;
      color: #fff;
      text-decoration: none;
      padding: 10px;
      border-radius: 4px;
      transition: background-color 0.3s;
      font-size: 0.95rem;
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
    <?php include '../includes/navbar.php'; ?>
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
                <!-- Heart Icon (favorite) -->
                <ion-icon name="heart-outline" class="heart-icon"></ion-icon>

                <!-- Room Image -->
                <?php if (!empty($room['image'])): ?>
                  <img src="../<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                <?php else: ?>
                  <img src="../assets/imgs/default-room.png" alt="Default Room Image">
                <?php endif; ?>

                <!-- Room Info Section -->
                <div class="room-info">
                  <div>
                    <!-- Top Row: Badge or location -->
                    <div class="top-row">
                      <!-- Example "Guest favorite" badge (conditional) -->
                      <span class="badge">Guest favorite</span>
                      <!-- Or you could display location if you have that in DB -->
                    </div>

                    <!-- Room Name -->
                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>

                    <!-- Host & Rating (placeholders for now) -->
                    <div class="hosted">Hosted by Nicole</div>
                    <div class="rating">
                      <ion-icon name="star"></ion-icon>
                      4.88
                    </div>

                    <!-- Price Info (with /night) -->
                    <div class="price-info">
                      Ksh <?php echo number_format($room['price'], 2); ?> / night
                    </div>

                    <!-- Capacity -->
                    <div class="capacity">
                      Capacity: <?php echo htmlspecialchars($room['capacity']); ?>
                    </div>
                  </div>

                  <!-- "Select Room" Button -->
                  <a href="room_details.php?id=<?php echo $room['id']; ?>" class="select-btn">Select Room</a>
                </div>
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
