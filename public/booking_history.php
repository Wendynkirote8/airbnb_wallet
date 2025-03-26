<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
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

// Fetch booking history
try {
    $stmt = $pdo->prepare("
        SELECT b.id, r.name AS room_name, b.days, b.total_cost, b.booking_date 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking History - Modern UI</title>

  <!-- Use the same CSS as your redesigned dashboard -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>

  <!-- Sidebar Navigation (same style as the new dashboard) -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>WeshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon> Dashboard</a></li>
        <!-- Highlight the Booking History link as active -->
        <li><a href="booking_history.php" class="active"><ion-icon name="receipt-outline"></ion-icon> Booking History</a></li>
        <li><a href="deposit.php"><ion-icon name="card-outline"></ion-icon> Deposit Funds</a></li>
        <!-- <li><a href="withdraw_new.php"><ion-icon name="cash-outline"></ion-icon> Withdraw Funds</a></li>-->
        <li><a href="redeem_points.php"><ion-icon name="gift-outline"></ion-icon> Redeem Points</a></li>
        <li><a href="messages.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Messages</a></li>
        <li><a href="settings.php"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        <li><a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Top Header -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search...">
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Page Content -->
    <section class="overview">
      <!-- Title Card -->
      <div class="welcome-card">
        <h1>Booking History</h1>
        <p>Review all your room bookings below.</p>
      </div>

      <!-- Booking History Table Card -->
      <div class="table-card">
        <div class="table-header">
          <h2>Recent Bookings</h2>
        </div>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Room</th>
                <th>Days</th>
                <th>Total Cost (Ksh)</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['days']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($booking['total_cost'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center;">No bookings found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Include your footer or navbar root if needed -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

</body>
</html>
