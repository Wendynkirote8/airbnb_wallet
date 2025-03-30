<?php
session_start();
require_once '../config/db_connect.php'; // Adjust path to your DB connection file

// Check if the 'id' parameter is present in the URL
if (!isset($_GET['id'])) {
    // If there's no ID, redirect back to listings or show an error
    header('Location: landing.php');
    exit();
}

$room_id = $_GET['id'];

// Fetch room details from the database using the room ID
$sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// If no room is found, redirect or show an error
if (!$room) {
    echo "Room not found.";
    exit();
}

// (Optional) Fetch multiple images for the room from a separate images table if you store them that way
// For example:
$image_sql = "SELECT image_url FROM room_images WHERE room_id = :room_id";
$image_stmt = $pdo->prepare($image_sql);
$image_stmt->execute(['room_id' => $room_id]);
$room_images = $image_stmt->fetchAll(PDO::FETCH_COLUMN); // Returns an array of image URLs
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($room['name']); ?> - Room Details</title>
  <link rel="stylesheet" href="../assets/css/landing.css"> <!-- Adjust to your stylesheet -->
  <style>
    /* Basic styling; adjust as needed */
    .details-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
    }
    .details-header {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    .details-header h1 {
      font-size: 1.8rem;
      color: #2a2185;
    }
    .details-header p {
      font-size: 1rem;
      color: #555;
    }
    /* Image Gallery */
    .image-gallery {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 10px;
      margin-bottom: 2rem;
    }
    .image-gallery img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 6px;
    }
    .main-image {
      grid-row: 1 / span 2; /* The main/large image spans two rows */
    }
    /* Room Info Section */
    .room-info {
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
    }
    .room-description,
    .booking-section {
      flex: 1 1 300px;
      background: #fff;
      border-radius: 6px;
      padding: 1rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .room-description h2 {
      margin-bottom: 0.5rem;
      color: #2a2185;
    }
    .room-description p {
      line-height: 1.5;
      margin-bottom: 1rem;
      color: #333;
    }
    .booking-section h2 {
      margin-bottom: 1rem;
      color: #2a2185;
    }
    .booking-section form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .booking-section form input,
    .booking-section form select {
      padding: 0.5rem;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .booking-section form button {
      padding: 0.6rem 1.2rem;
      background-color: #2a2185;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
    }
    .booking-section form button:hover {
      background-color: #1c193f;
    }
  </style>
</head>
<body>

<!-- Top header or nav can be included here -->
<?php // include '../includes/top_header.php'; ?>

<div class="details-container">
  <!-- Room Title & Basic Info -->
  <div class="details-header">
    <h1><?php echo htmlspecialchars($room['name']); ?></h1>
    <!-- You can display location, rating, etc. here -->
    <p><?php echo htmlspecialchars($room['location'] ?? 'Location not specified'); ?> &bull; 
       <?php echo htmlspecialchars($room['capacity']); ?> guests &bull; 
       <?php echo htmlspecialchars($room['bedrooms'] ?? '1'); ?> bedroom &bull; 
       <?php echo htmlspecialchars($room['bathrooms'] ?? '1'); ?> bath
    </p>
  </div>

  <!-- Image Gallery (show multiple images if available) -->
  <div class="image-gallery">
    <?php if (!empty($room_images)): ?>
      <!-- Large Main Image -->
      <img src="<?php echo $room_images[0]; ?>" alt="Main Image" class="main-image">
      <!-- Additional Images -->
      <?php for ($i = 1; $i < count($room_images); $i++): ?>
        <img src="<?php echo $room_images[$i]; ?>" alt="Additional Image <?php echo $i; ?>">
      <?php endfor; ?>
    <?php else: ?>
      <!-- If no images in DB, show a placeholder or the room's featured image column -->
      <img src="https://via.placeholder.com/800x600?text=No+Images+Available" alt="Placeholder" class="main-image">
    <?php endif; ?>
  </div>

  <!-- Room Description & Booking Section -->
  <div class="room-info">
    <div class="room-description">
      <h2>About this place</h2>
      <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
      <p><strong>Amenities:</strong> <?php echo htmlspecialchars($room['amenities'] ?? 'No amenities listed'); ?></p>
    </div>

    <div class="booking-section">
      <h2>Book your stay</h2>
      <p><strong>Price:</strong> Ksh <?php echo number_format($room['price'], 2); ?> / night</p>

      <!-- Example booking form (customize to your needs) -->
      <form action="process_booking.php" method="POST">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        
        <label for="checkin_date">Check-in:</label>
        <input type="date" id="checkin_date" name="checkin_date" required>
        
        <label for="checkout_date">Check-out:</label>
        <input type="date" id="checkout_date" name="checkout_date" required>
        
        <label for="guests">Guests:</label>
        <select name="guests" id="guests">
          <?php for ($g = 1; $g <= $room['capacity']; $g++): ?>
            <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
          <?php endfor; ?>
        </select>
        
        <button type="submit">Reserve</button>
      </form>
    </div>
  </div>
</div>

<!-- Footer can be included here -->
<?php // include '../includes/footer.php'; ?>

</body>
</html>
