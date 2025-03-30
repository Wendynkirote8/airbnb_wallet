<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require '../config/db_connect.php'; // This should set up a PDO instance in $pdo

use PHPGangsta_GoogleAuthenticator;

// 1) Ensure a user session exists (either fully logged in or pending 2FA)
if (!isset($_SESSION['pending_2fa_user_id']) && !isset($_SESSION['user_id'])) {
    die("Unauthorized access. No user session found.");
}

// 2) Determine which user_id to use:
$userId = $_SESSION['user_id'] ?? $_SESSION['pending_2fa_user_id'];

// 3) Fetch user's existing secret from the database using PDO
$stmt = $pdo->prepare("SELECT totp_secret FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$secret = $stmt->fetchColumn();

// 4) If no secret is set, redirect them to enable_2fa.php
if (empty($secret)) {
    header("Location: enable_2fa.php");
    exit;
}

// 5) Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code     = $_POST['code'] ?? '';
    $remember = isset($_POST['remember']);

    $g = new PHPGangsta_GoogleAuthenticator();
    // 6) Verify the TOTP code (allowing ±60 seconds leeway)
    $isValid = $g->verifyCode($secret, $code, 2);

    if ($isValid) {
        // 7) Mark the user as fully logged in and 2FA verified
        $_SESSION['user_id']      = $userId;
        $_SESSION['2fa_verified'] = true;
        unset($_SESSION['pending_2fa_user_id']);

        // 8) Handle "Remember this device" if the checkbox was checked
        if ($remember) {
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $stmt = $pdo->prepare("
                INSERT INTO trusted_devices (user_id, token_hash, expires_at, user_agent)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $tokenHash, $expiresAt, $userAgent]);

            // Set the cookie for 7 days
            setcookie('remember_2fa', $token, time() + (86400 * 7), "/", "", false, true);
        }

        // 9) Redirect to the dashboard or your desired page
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Two-Factor Authentication</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .verify-container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .verify-container h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .btn-custom {
            background-color: #007bff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2 class="text-center">Verify Your 2FA Code</h2>
        <form method="POST">
            <div class="form-group">
                <label for="code">Enter the 6-digit code from your app:</label>
                <input type="text" name="code" id="code" class="form-control" placeholder="6-digit code" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Remember this device for 7 days</label>
            </div>
            <button type="submit" class="btn btn-custom btn-block">Verify</button>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Optional JavaScript and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
