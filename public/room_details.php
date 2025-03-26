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

// Get the room id from the query string
if (!isset($_GET['id'])) {
    die("No room selected.");
}
$room_id = $_GET['id'];

// Fetch room details
try {
    $stmt = $pdo->prepare("SELECT id, name, price, description FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching room details: " . $e->getMessage());
}

if (!$room) {
    die("Room not found.");
}

// Variable to hold a message for feedback (success or error)
$feedbackMessage = "";

// Process booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the number of days from form input
    $days = isset($_POST['days']) ? (int)$_POST['days'] : 1;

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
        // If there are already 2 bookings, this is the third booking and discount applies
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

        // Retrieve the user's wallet balance from the wallets table
        $stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $walletData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$walletData) {
            throw new Exception("Wallet not found for the user.");
        }
        $wallet_balance = $walletData['balance'];

        // Check if wallet balance is sufficient
        if ($wallet_balance < $total_cost) {
            throw new Exception("Insufficient wallet funds. Your current wallet balance is ksh. " . number_format($wallet_balance, 2));
        }

        // Deduct the total cost from the wallet balance
        $new_balance = $wallet_balance - $total_cost;
        $stmt = $pdo->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
        $stmt->execute([$new_balance, $user_id]);

        // Insert booking record into the bookings table
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, days, total_cost, booking_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $room_id, $days, $total_cost]);

        // Commit transaction
        $pdo->commit();

        $feedbackMessage = "Booking successful! Total cost: ksh. " . number_format($total_cost, 2) . ". Available balance: ksh. " . number_format($new_balance, 2);
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
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
    : "../assets/imgs/customer01.jpg"; // Fallback image
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Book Room - <?php echo htmlspecialchars($room['name']); ?></title>
  
  <!-- Unified Styles from your dashboard -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <style>
    .booking-form {
      max-width: 400px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .booking-form input[type="number"],
    .booking-form button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
    }
    .booking-form button {
      background-color: #2a2185;
      color: #fff;
      border: none;
      cursor: pointer;
    }
    .booking-form button:hover {
      background-color: #1c193f;
    }
    /* Style for the feedback message */
    .message {
      max-width: 400px;
      margin: 20px auto;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
    }
    .message.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
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
        <h1>Book Room</h1>
        <p>Complete your booking by filling out the form below.</p>
      </div>

      <!-- Display feedback message if available -->
      <?php if (!empty($feedbackMessage)): ?>
        <div class="message <?php echo (strpos($feedbackMessage, "successful") !== false) ? "success" : "error"; ?>">
          <?php echo htmlspecialchars($feedbackMessage); ?>
        </div>
      <?php endif; ?>

      <div class="booking-form">
        <h2>Book <?php echo htmlspecialchars($room['name']); ?></h2>
        <p><?php echo htmlspecialchars($room['description']); ?></p>
        <p>Price per day: ksh. <?php echo number_format($room['price'], 2); ?></p>
        <p>Note: Book for 4 or more days, or if this is your third booking for this room, to receive a 10% discount!</p>
        <form method="POST" action="">
          <label for="days">Number of Days:</label>
          <input type="number" id="days" name="days" min="1" value="1" required>
          <button type="submit">Confirm Booking</button>
        </form>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>
</body>
</html>
