<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require '../config/db_connect.php'; // This should define $pdo

use PHPGangsta_GoogleAuthenticator;

// 1) Ensure the user is logged in or at least pending 2FA.
if (!isset($_SESSION['user_id']) && !isset($_SESSION['pending_2fa_user_id'])) {
    die("You must be logged in to enable 2FA.");
}

// 2) Determine which user we're working with.
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['pending_2fa_user_id'];

// 3) Check if this user already has a TOTP secret.
$stmt = $pdo->prepare("SELECT totp_secret FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$existingSecret = $stmt->fetchColumn();

// 4) If they already have 2FA set up, skip to verification.
if (!empty($existingSecret)) {
    header("Location: verify_2fa.php");
    exit;
}

// 5) User does NOT have TOTP set up. Create a new secret.
$ga = new PHPGangsta_GoogleAuthenticator();

// Store the new secret in session so the user won't get a new secret on page refresh.
if (empty($_SESSION['new_totp_secret'])) {
    $_SESSION['new_totp_secret'] = $ga->createSecret();
}
$secret = $_SESSION['new_totp_secret'];

// 6) Generate the QR code URL (replace 'YourAppName' with your app's name).
$qrCodeUrl = $ga->getQRCodeGoogleUrl('YourAppName', $secret);

// 7) Handle form submission (when the user enters the code to confirm setup).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';

    // Verify the user's code against the newly generated secret (with ±60 seconds tolerance).
    $isValid = $ga->verifyCode($secret, $code, 2);

    if ($isValid) {
        // 8) Save the new TOTP secret in the database.
        $stmt = $pdo->prepare("UPDATE users SET totp_secret = ? WHERE user_id = ?");
        $stmt->execute([$secret, $userId]);

        // 9) Mark the user as fully 2FA-enabled.
        $_SESSION['2fa_verified'] = true;
        // If they were pending (e.g. from a Google login), promote them to a full login.
        $_SESSION['user_id'] = $userId;
        unset($_SESSION['pending_2fa_user_id']);
        // Clear the temporary secret from session.
        unset($_SESSION['new_totp_secret']);

        // 10) Redirect to the dashboard or desired page.
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
    <title>Enable Two-Factor Authentication</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .mfa-container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .mfa-container h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .qr-code {
            display: block;
            margin: 20px auto;
            max-width: 200px;
        }
        .secret-code {
            font-family: monospace;
            background: #eee;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
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
    <div class="mfa-container">
        <h2 class="text-center">Enable Two-Factor Authentication</h2>
        <p>Scan the QR code below with your authenticator app (e.g., Google Authenticator or Authy):</p>
        <div class="text-center">
            <img src="<?php echo htmlspecialchars($qrCodeUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code" class="qr-code">
        </div>
        <p class="text-center">Or manually enter this secret:</p>
        <p class="secret-code text-center"><?php echo htmlspecialchars($secret, ENT_QUOTES, 'UTF-8'); ?></p>
        
        <form method="POST" class="mt-4">
            <div class="form-group">
                <label for="code">Enter the 6-digit code from your app:</label>
                <input type="text" name="code" id="code" class="form-control" placeholder="6-digit code" required>
            </div>
            <button type="submit" class="btn btn-custom btn-block">Enable 2FA</button>
        </form>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Optional JavaScript and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
