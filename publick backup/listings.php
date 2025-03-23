<?php
session_start();
require_once '../config/db_connect.php'; // Ensure $pdo is set up

// Fetch all available rooms (adjust query as needed)
$stmt = $pdo->query("SELECT id, name, description, price, capacity, image FROM rooms ORDER BY created_at DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/navbar.php'; ?>
  <style>
    /* Global Styles */
    
    
    h1 {
      text-align: center;
      color: #2a2185;
      margin-bottom: 20px;
    }
    /* Container */
    .container2 {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 10px;
    }
    /* Rooms Grid */
    .rooms-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    /* Room Card */
    .room-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
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
      text-align: left;
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
    /* Select Room Button */
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

  <div class="container2">
    <h1>Available Rooms</h1>
    <?php if ($rooms && count($rooms) > 0): ?>
      <div class="rooms-grid">
        <?php foreach ($rooms as $room): ?>
          <div class="room-card">
            <?php if (!empty($room['image'])): ?>
              <img src="../<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
            <?php else: ?>
              <!-- Fallback image -->
              <img src="../assets/imgs/default-room.png" alt="Default Room Image">
            <?php endif; ?>
            <div class="room-info">
              <h3><?php echo htmlspecialchars($room['name']); ?></h3>
              <p><?php echo htmlspecialchars($room['description']); ?></p>
              <p class="price">$<?php echo number_format($room['price'], 2); ?></p>
              <p class="capacity">Capacity: <?php echo htmlspecialchars($room['capacity']); ?></p>
            </div>
            <a href="room_details.php?id=<?php echo $room['id']; ?>" class="select-btn">Select Room</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p style="text-align: center;">No rooms available at this time.</p>
    <?php endif; ?>
  </div>
  <?php include '../includes/navbarroot.php'; ?>

