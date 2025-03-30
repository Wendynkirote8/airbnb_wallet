<?php
session_start();
require_once '../config/db_connect.php';

// Ensure admin is authenticated (adjust as needed)
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

        // Update booking status to confirmed
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$booking_id]);

        // Retrieve booking details to notify the user
        $stmt = $pdo->prepare("SELECT user_id FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $user_id = $booking['user_id'];
            // Prepare a notification message
            $message = "Your booking (ID: {$booking_id}) has been confirmed.";
            // Insert notification into a notifications table
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

// Fetch all pending bookings
$stmt = $pdo->prepare("
    SELECT b.id as booking_id, b.user_id, b.room_id, b.days, b.total_cost, b.booking_date,
           u.full_name, r.name as room_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'pending'
    ORDER BY b.booking_date ASC
");
$stmt->execute();
$pendingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Room - Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      padding: 20px;
      background: #f4f6f8;
    }
    h1 {
      margin-bottom: 20px;
      color: #2a2185;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    table th, table td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    table th {
      background: #2a2185;
      color: #fff;
    }
    .message {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
    }
    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    form {
      margin: 0;
    }
    button {
      padding: 8px 14px;
      background: #2a2185;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #1c193f;
    }
  </style>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <div class="container">
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>

  <?php if (isset($success_message)): ?>
    <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
  <?php elseif (isset($error_message)): ?>
    <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <?php if (count($pendingBookings) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>User</th>
          <th>Room</th>
          <th>Days</th>
          <th>Total Cost</th>
          <th>Booking Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendingBookings as $booking): ?>
          <tr>
            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
            <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['days']); ?></td>
            <td><?php echo "ksh. " . number_format($booking['total_cost'], 2); ?></td>
            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
            <td>
              <form method="POST">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                <button type="submit" name="confirm_booking">Confirm Booking</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No pending bookings at this time.</p>
  <?php endif; ?>
</body>
</html>
