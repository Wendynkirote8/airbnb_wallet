<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/db_connect.php'; // Ensure $pdo is set up

// Ensure user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["user_id"];

// Get the room id from the query string for booking (if provided)
$room_id = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch room details if a room is selected for booking
$room = null;
if ($room_id) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, price, description, image FROM rooms WHERE id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching room details: " . $e->getMessage());
    }
    if (!$room) {
        die("Room not found.");
    }
}

// Variable to hold a message for feedback (success or error)
$feedbackMessage = "";

// Process booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['days'])) {
    // Retrieve the number of days from form input
    $days = (int) $_POST['days'];

    // Define discount rate (10% discount)
    $discount_rate = 0.10;
    $apply_discount = false;

    // Condition 1: Booking is for 4 or more days
    if ($days >= 4) {
        $apply_discount = true;
    } else {
        // Condition 2: Check if the user has booked this same room at least twice already
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND room_id = ?");
        $stmt->execute([$user_id, $room_id]);
        $previousBookings = $stmt->fetchColumn();
        if ($previousBookings >= 2) {
            $apply_discount = true;
        }
    }

    // Get the room price (price per day)
    $room_price = $room['price'];

    // Apply discount if eligible
    if ($apply_discount) {
        $room_price = $room_price - ($room_price * $discount_rate);
    }

    // Calculate the total cost
    $total_cost = $room_price * $days;

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Retrieve the user's wallet balance and wallet id from the wallets table
        $stmt = $pdo->prepare("SELECT wallet_id, balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $walletData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$walletData) {
            throw new Exception("Wallet not found for the user.");
        }
        $wallet_balance = $walletData['balance'];
        $wallet_id_db = $walletData['wallet_id'];

        // Check if wallet balance is sufficient
        if ($wallet_balance < $total_cost) {
            throw new Exception("Insufficient wallet funds. Your current wallet balance is ksh. " . number_format($wallet_balance, 2));
        }

        // Deduct the total cost from the wallet balance
        $new_balance = $wallet_balance - $total_cost;
        $stmt = $pdo->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
        $stmt->execute([$new_balance, $user_id]);

        // Insert booking record into the bookings table (status defaulted as 'pending')
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, days, total_cost, booking_date, status) VALUES (?, ?, ?, ?, NOW(), 'pending')");
        $stmt->execute([$user_id, $room_id, $days, $total_cost]);
        $booking_id = $pdo->lastInsertId();

        // Generate a unique alphanumeric transaction ID
        $transaction_id = uniqid('tx_');

        // Insert a row into the transactions table
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, wallet_id, amount, transaction_type, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$transaction_id, $wallet_id_db, $total_cost, 'booking', 'completed']);

        // Insert a payment log record into the payment_logs table
        $provider = "w"; // short code for wallet
        $reference = "booking_id: " . $booking_id;
        $payment_status = "completed";
        $stmt = $pdo->prepare("INSERT INTO payment_logs (user_id, transaction_id, provider, reference, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $transaction_id, $provider, $reference, $payment_status]);

        // Commit transaction
        $pdo->commit();

        $feedbackMessage = "Booking successful! Total cost: ksh. " . number_format($total_cost, 2) . ". Available balance: ksh. " . number_format($new_balance, 2);
    } catch (Exception $e) {
        $pdo->rollBack();
        $feedbackMessage = "Error: " . $e->getMessage();
    }
}

// Fetch user details for header display
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"])
    ? "../uploads/" . $user["profile_picture"]
    : "../assets/imgs/customer01.jpg";

// Fetch user's bookings (joined with room details)
$stmt = $pdo->prepare("SELECT b.id as booking_id, b.room_id, b.days, b.total_cost, b.booking_date, 'pending' as status, r.name as room_name 
                       FROM bookings b 
                       LEFT JOIN rooms r ON b.room_id = r.id 
                       WHERE b.user_id = ? 
                       ORDER BY b.booking_date DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all rooms for the grid display
$stmt = $pdo->query("SELECT id, name, price, description, image FROM rooms ORDER BY id DESC");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book Room<?php echo $room ? " - " . htmlspecialchars($room['name']) : ""; ?></title>
  <!-- Retain your existing dashboard stylesheet -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">
  <style>
    /* --- Updated Styling for Main Content Sections --- */
    /* Booking Form, My Bookings, and Explore Rooms sections */
    .booking-form, .bookings-section, .rooms-grid-section {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .booking-form h2, .bookings-section h2, .rooms-grid-section h2 {
      margin-top: 0;
      font-size: 1.5rem;
      color: #2a2185;
      border-bottom: 2px solid #2a2185;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    /* Booking Form */
    .booking-form input[type="number"],
    .booking-form button {
      width: 100%;
      padding: 14px;
      margin: 10px 0;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .booking-form button {
      background: #2a2185;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    .booking-form button:hover {
      background: #1c193f;
    }
    /* Feedback Message */
    .message {
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
      font-size: 1rem;
    }
    .message.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .message.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    /* Bookings Section Table */
    .bookings-section table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    .bookings-section th, .bookings-section td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
      font-size: 0.95rem;
    }
    .bookings-section th {
      background: #2a2185;
      color: #fff;
    }
    .cancel-btn {
      padding: 8px 14px;
      background: #e74c3c;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
      font-size: 0.9rem;
    }
    .cancel-btn:hover {
      background: #c0392b;
    }
    /* Rooms Grid */
    .rooms-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 15px;
    }
    .room-card {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .room-card:hover {
      transform: translateY(-3px);
    }
    .room-image img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      display: block;
      border-bottom: 1px solid #eee;
    }
    .room-details {
      padding: 15px;
    }
    .room-details h3 {
      margin: 0 0 10px;
      font-size: 1.2rem;
      color: #2a2185;
    }
    .room-details p {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 8px;
    }
    .room-details .room-price {
      font-weight: bold;
      color: #2a2185;
      font-size: 1rem;
    }
    .room-details a {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background: #2a2185;
      color: #fff;
      border-radius: 6px;
      transition: background 0.3s;
      font-size: 0.9rem;
    }
    .room-details a:hover {
      background: #1c193f;
    }
    /* Responsive adjustments for smaller screens */
    @media (max-width: 768px) {
      .rooms-grid {
        grid-template-columns: 1fr;
      }
      .header {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
  <!-- Ionicons for icons (if needed) -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <!-- Sidebar Navigation (unchanged) -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <?php include '../includes/navbar.php'; ?>
  </aside>

  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Top Header (unchanged) -->
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

    <!-- Page Content -->
    <section class="overview">
      <div class="welcome-card">
        <h1>Book Room</h1>
        <p>Complete your booking by filling out the form below.</p>
      </div>

      <!-- Feedback Message -->
      <?php if (!empty($feedbackMessage)): ?>
        <div class="message <?php echo (strpos($feedbackMessage, "successful") !== false) ? "success" : "error"; ?>">
          <?php echo htmlspecialchars($feedbackMessage); ?>
        </div>
      <?php endif; ?>

      <!-- Booking Form (if a room is selected) -->
      <?php if ($room): ?>
      <div class="booking-form">
        <h2>Book <?php echo htmlspecialchars($room['name']); ?></h2>
        <?php if (!empty($room['image'])): ?>
          <div class="room-image">
            <img src="../<?php echo htmlspecialchars($room['image']); ?>" alt="Room Image">
          </div>
        <?php endif; ?>
        <p><?php echo htmlspecialchars($room['description']); ?></p>
        <p>Price per day: ksh. <?php echo number_format($room['price'], 2); ?></p>
        <p>Note: Book for 4 or more days or if this is your third booking for this room to receive a 10% discount!</p>
        <form method="POST" action="">
          <label for="days">Number of Days:</label>
          <input type="number" id="days" name="days" min="1" value="1" required>
          <button type="submit">Confirm Booking</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- My Bookings Section -->
      <div class="bookings-section">
        <h2>My Bookings</h2>
        <?php if ($bookings && count($bookings) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Room</th>
                <th>Days</th>
                <th>Total Cost</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $b): ?>
                <tr>
                  <td><?php echo htmlspecialchars($b['room_name']); ?></td>
                  <td><?php echo htmlspecialchars($b['days']); ?></td>
                  <td><?php echo "ksh. " . number_format($b['total_cost'], 2); ?></td>
                  <td><?php echo htmlspecialchars($b['booking_date']); ?></td>
                  <td><?php echo htmlspecialchars(ucfirst($b['status'])); ?></td>
                  <td>
                    <?php if (strtolower($b['status']) === 'pending'): ?>
                      <a class="cancel-btn" href="cancel_booking.php?id=<?php echo $b['booking_id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>You have not booked any rooms yet.</p>
        <?php endif; ?>
      </div>

      <!-- Explore Rooms Section -->
      <div class="rooms-grid-section">
        <h2>Explore Rooms</h2>
        <?php if ($rooms && count($rooms) > 0): ?>
          <div class="rooms-grid">
            <?php foreach ($rooms as $r): ?>
              <div class="room-card">
                <?php if (!empty($r['image'])): ?>
                  <div class="room-image">
                    <img src="../<?php echo htmlspecialchars($r['image']); ?>" alt="Room Image">
                  </div>
                <?php endif; ?>
                <div class="room-details">
                  <h3><?php echo htmlspecialchars($r['name']); ?></h3>
                  <p><?php echo htmlspecialchars($r['description']); ?></p>
                  <p class="room-price">Ksh <?php echo number_format($r['price'], 2); ?> / night</p>
                  <a href="room_details.php?id=<?php echo $r['id']; ?>">View Details</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>No rooms available.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>
</body>
</html>
