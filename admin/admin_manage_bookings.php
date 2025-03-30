<?php
session_start();
require_once '../config/db_connect.php';

// Ensure admin is authenticated
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Process booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $booking_id = $_POST['booking_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Update booking status to 'booked'
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'booked' WHERE id = ?");
        $stmt->execute([$booking_id]);

        // Retrieve booking details to notify the user
        $stmt = $pdo->prepare("SELECT user_id FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $user_id = $booking['user_id'];
            // Prepare a notification message
            $message = "Your booking (ID: {$booking_id}) has been confirmed.";
            // Insert notification into the notifications table
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $message]);
        }

        $pdo->commit();
        $success_message = "Booking confirmed and user notified.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all bookings (including booked, pending, or canceled)
$sql = "SELECT b.id, b.room_id, b.user_id, b.status, r.name as room_name 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        ORDER BY b.id ASC";
$stmt = $pdo->query($sql);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings - Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/admin_style.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <style>
    /* Status colors */
    .status-booked {
      color: green;
      font-weight: bold;
    }
    .status-canceled {
      color: red;
      font-weight: bold;
    }
    .status-pending {
      color: orange;
      font-weight: bold;
    }
    /* Notification messages */
    .success {
      color: green;
      background: #e0ffe0;
      padding: 10px;
      border-radius: 5px;
    }
    .error {
      color: red;
      background: #ffe0e0;
      padding: 10px;
      border-radius: 5px;
    }
    .booking-actions {
    display: grid;
    grid-template-columns: repeat(3, auto);
    gap: 10px;
    align-items: center;
  }

  /* Base styling for all action buttons */
  .action-btn {
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.3s ease;
    text-align: center;
  }

  /* Confirm button styling */
  .action-btn.confirm {
    background-color: #2196F3; /* Blue */
    color: white;
  }
  .action-btn.confirm:hover {
    background-color: #0b7dda;
  }

  /* Edit button styling */
  .action-btn.edit {
    background-color: #4CAF50; /* Green */
    color: white;
  }
  .action-btn.edit:hover {
    background-color: #45a049;
  }

  /* Delete button styling */
  .action-btn.delete {
    background-color: #f44336; /* Red */
    color: white;
  }
  .action-btn.delete:hover {
    background-color: #da190b;
  }

  /* Styling for disabled button */
  .action-btn.disabled-btn {
    background-color: #ccc;
    color: #666;
    cursor: not-allowed;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    text-align: center;
  }

  /* Remove default form styling */
  .booking-actions form {
    margin: 0;
  }
  </style>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>
    <!-- Main Content Area -->
    <div class="main">
      <!-- Topbar -->
      <div class="topbar">
        <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
        <div class="search">
          <input type="text" placeholder="Search here">
        </div>
        <div class="user">
          <img src="../assets/imgs/default-profile.png" alt="Admin Profile">
        </div>
      </div>
      <!-- Content -->
      <div class="content">
        <h2>Manage Bookings</h2>
        <?php if (isset($success_message)) echo "<p class='success'>{$success_message}</p>"; ?>
        <?php if (isset($error_message)) echo "<p class='error'>{$error_message}</p>"; ?>
        <?php if (count($bookings) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Room</th>
                <th>User</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $booking): ?>
              <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                <td>
                  <?php 
                    $status = $booking['status'];
                    $status_class = 'status-pending';
                    if ($status === 'booked') {
                        $status_class = 'status-booked';
                    } elseif ($status === 'canceled') {
                        $status_class = 'status-canceled';
                    }
                  ?>
                  <span class="<?php echo $status_class; ?>">
                    <?php echo htmlspecialchars(ucfirst($status)); ?>
                  </span>
                </td>
                <td>
                  <!-- Confirm Booking Button -->
                  <div class="booking-actions">
                    <?php if ($booking['status'] === 'pending'): ?>
                      <form action="" method="POST" onsubmit="return confirm('Are you sure you want to confirm this booking?');">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <button type="submit" name="confirm_booking" class="action-btn confirm">Confirm Booking</button>
                      </form>
                    <?php else: ?>
                      <!-- Disabled button if booked or canceled -->
                      <button class="action-btn disabled-btn" disabled>Confirm Booking</button>
                    <?php endif; ?>
                </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No bookings found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
