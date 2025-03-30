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

// Fetch booking history including all statuses (booked, canceled, etc.)
try {
    $stmt = $pdo->prepare("
        SELECT b.id AS booking_id, r.name AS room_name, b.days, b.total_cost, b.booking_date, b.status 
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

  <style>
    /* Updated styling for the table card */
    .table-card {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .table-card h2 {
      margin-top: 0;
      font-size: 1.5rem;
      color: #2a2185;
      border-bottom: 2px solid #2a2185;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .table-responsive {
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
      font-size: 0.95rem;
    }
    th {
      background: #2a2185;
      color: #fff;
    }
    /* Status styling: if the booking is canceled, show in red */
    .status-canceled {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>WeshPAY</h2>
    </div>
    <?php include '../includes/navbar.php'; ?>
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
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['days']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($booking['total_cost'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    <td>
                      <?php 
                        // If status is canceled, apply the "status-canceled" class
                        $status = htmlspecialchars(ucfirst($booking['status']));
                        if (strtolower($booking['status']) === 'canceled') {
                          echo "<span class='status-canceled'>$status</span>";
                        } else {
                          echo $status;
                        }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No bookings found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>
</body>
</html>
