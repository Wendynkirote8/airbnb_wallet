<?php
session_start();
require '../config/db_connect.php';

// Redirect if not authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user's current loyalty points
$stmt = $pdo->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$user_id]);
$userPoints = $stmt->fetch(PDO::FETCH_ASSOC)["points"] ?? 0;

// Calculate equivalent money (10 points = Ksh 1)
$equivalent_money = $userPoints / 10;

// (Optional) Fetch user details for header display
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
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>weshPAY - Redeem Loyalty Points</title>

  <!-- Unified Styles (same as your new dashboard) -->
  <link rel="stylesheet" href="../assets/css/dashboard_new.css">

  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
  <!-- =============== Sidebar Navigation ================ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <h2>weshPAY</h2>
    </div>
    <nav class="sidebar-nav">
      <ul>
        <!-- Link to your main dashboard -->
        <li>
          <a href="dashboard.php">
            <ion-icon name="grid-outline"></ion-icon>
            Dashboard
          </a>
        </li>
        <!-- Additional navigation links -->
        <li>
          <a href="#">
            <ion-icon name="bed-outline"></ion-icon>
            Available AirBNB
          </a>
        </li>
        <li>
          <a href="#">
            <ion-icon name="chatbubble-outline"></ion-icon>
            Messages
          </a>
        </li>
        <li>
          <a href="#">
            <ion-icon name="help-outline"></ion-icon>
            Help
          </a>
        </li>
        <li>
          <a href="#">
            <ion-icon name="settings-outline"></ion-icon>
            Settings
          </a>
        </li>
        <li>
          <a href="#">
            <ion-icon name="lock-closed-outline"></ion-icon>
            Password
          </a>
        </li>
        <li>
          <a href="logout.php">
            <ion-icon name="log-out-outline"></ion-icon>
            Sign Out
          </a>
        </li>
      </ul>
    </nav>
  </aside>

  <!-- =============== Main Content ================ -->
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
      <!-- Title Card -->
      <div class="welcome-card">
        <h1>Redeem Your Loyalty Points</h1>
        <p>
          You have <span class="points_redeem"><?php echo number_format($userPoints); ?></span> points.
          <br>
          Equivalent Value: <span class="money_redeem">Ksh <?php echo number_format($equivalent_money, 2); ?></span>
        </p>
      </div>

      <!-- Redeem Form Card -->
      <div class="form-card">
        <form action="../scripts/redeem_points_action.php" method="POST" class="deposit-form">
          <label for="redeem_points">Enter Points to Redeem:</label>
          <input 
            type="number" 
            name="redeem_points" 
            id="redeem_points" 
            placeholder="Enter Points to Redeem" 
            min="10" 
            max="<?php echo $userPoints; ?>" 
            required
          />
          <button type="submit">Redeem Points</button>
        </form>
      </div>
    </section>

    <!-- Optional footer or additional navigation -->
    <?php include '../includes/navbarroot.php'; ?>
  </div>

  <!-- =========== Scripts =========  -->
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
