<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/db_connect.php';

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

// Fetch user details
$stmt = $pdo->prepare("SELECT full_name, email, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$full_name = $user['full_name'] ?? 'Guest';
$email = $user['email'] ?? 'guest@example.com';
$profile_picture = $user['profile_picture'] ? "../uploads/" . $user['profile_picture'] : "../assets/imgs/default-profile.png";
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wesh AirBNB Pay</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Wesh pay</span>
                    </a>
                </li>

                <li>
                    <a onclick="window.location.href='../public/index.php';" style="cursor: pointer;">
                        <span class="icon">
                            <ion-icon name="grid-outline"></ion-icon>

                            <!-- <ion-icon name="home-outline"></ion-icon> -->
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="bed-outline"></ion-icon>
                        </span>
                        <span class="title">Available AirBNB</span>
                    </a>
                </li>

                <li>
                    <a href="transactions.php">
                        <span class="icon">
                            <ion-icon name="receipt-outline"></ion-icon>
                        </span>
                        <span class="title">Transaction History</span>
                    </a>
                </li>

                <li>
                    <a href="deposit.php">
                        <span class="icon">
                            <ion-icon name="card-outline"></ion-icon>
                        </span>
                        <span class="title">Deposit Funds</span>
                    </a>
                </li>

                <li>
                    <a href="withdraw.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">withdraw</span>
                    </a>
                </li>

                <li>
                    <a href="loyalty_points.php">
                        <span class="icon">
                        <ion-icon name="trophy-outline"></ion-icon>
                        </span>
                        <span class="title">Loyalty Points</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                        <ion-icon name="gift-outline"></ion-icon>
                        </span>
                        <span class="title">Redeem Points</span>
                    </a>
                </li>


                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Messages</span>
                    </a>
                </li>

                <!-- <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="help-outline"></ion-icon>
                        </span>
                        <span class="title">Help</span>
                    </a>
                </li> -->

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="settings-outline"></ion-icon>
                        </span>
                        <span class="title">Settings</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </span>
                        <span class="title">Password</span>
                    </a>
                </li>

                <li>
                <a onclick="window.location.href='../public/logout.php';" style="cursor: pointer;">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                

                <!-- User Profile Icon -->
            <div class="user" onclick="toggleProfileDropdown()">
                <img src="<?php echo $profile_picture; ?>" alt="User Profile">
            </div>

            <!-- Profile Dropdown -->
            <div id="profileDropdown" class="dropdown">
                <div class="dropdown-content">
                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
                    <p class="user-name"><strong><?php echo $full_name; ?></strong></p>
                    <p class="user-email"><?php echo $email; ?></p>
                    <button onclick="window.location.href='update_profile.php'">Edit Profile</button>
                    <button onclick="window.location.href='../public/logout.php'">Logout</button>
                </div>
            </div>

            <!-- JavaScript -->
            <script>
                function toggleProfileDropdown() {
                    let dropdown = document.getElementById("profileDropdown");
                    dropdown.classList.toggle("show");
                }

                // Close dropdown when clicking outside
                window.onclick = function(event) {
                    if (!event.target.closest(".user")) {
                        document.getElementById("profileDropdown").classList.remove("show");
                    }
                }
            </script>
           </div>

  <!-- =========== Scripts =========  -->
    <script src="../assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>  

    </body>
</html>