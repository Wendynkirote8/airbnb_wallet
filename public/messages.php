<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user details for the header display
$stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$full_name = $user ? $user["full_name"] : "User";
$profile_picture = $user && !empty($user["profile_picture"]) 
    ? "../uploads/" . $user["profile_picture"] 
    : "../assets/imgs/default-user.png";

// Fetch messages for the current user
try {
    $stmt = $pdo->prepare("
        SELECT m.message_id, m.subject, m.content, m.created_at, u.full_name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.recipient_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching messages: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>weshPAY - Messages</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ======= Unified Styles (Same as your new dashboard) ======= -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style>
    .messages-table {
      width: 100%;
      border-collapse: collapse;
    }
    .messages-table th, .messages-table td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }
    .messages-table th {
      background-color: #f2f2f2;
      color: #333;
    }
    .message-subject {
      font-weight: bold;
      color: #2a2185;
    }
    .no-messages {
      text-align: center; 
      margin-top: 20px; 
      font-size: 1rem;
      color: #555;
    }
  </style>
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
    <!-- Topbar (Header) -->
    <header class="header">
      <div class="header-search">
        <input type="text" placeholder="Search messages..." />
        <ion-icon name="search-outline"></ion-icon>
      </div>
      <div class="header-user">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User Profile">
        <span><?php echo htmlspecialchars($full_name); ?></span>
      </div>
    </header>

    <!-- Messages Page Content -->
    <section class="overview">
      <div class="welcome-card">
        <h1>Your Messages</h1>
        <p>Check your inbox for recent updates.</p>
      </div>

      <div class="table-card">
        <div class="table-header">
          <h2>Inbox</h2>
        </div>
        <div class="table-responsive">
          <?php if (!empty($messages)): ?>
            <table class="messages-table">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>From</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($messages as $msg): ?>
                  <tr>
                    <td>
                      <span class="message-subject">
                        <?php echo htmlspecialchars($msg['subject']); ?>
                      </span>
                      <br>
                      <!-- You could truncate content or display it in a modal -->
                      <small><?php echo htmlspecialchars(substr($msg['content'], 0, 50)); ?>...</small>
                    </td>
                    <td><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['created_at']); ?></td>
                    <td>
                      <a href="view_message.php?id=<?php echo $msg['message_id']; ?>">View</a> | 
                      <a href="delete_message.php?id=<?php echo $msg['message_id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="no-messages">No messages found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php include '../includes/navbarroot.php'; ?>
  </div>

  <script src="../assets/js/dashboard_new.js"></script>
</body>
</html>
