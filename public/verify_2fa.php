<?php
session_start();
require_once '../vendor/autoload.php';
require '../config/db_connect.php';

use PHPGangsta_GoogleAuthenticator;

if (!isset($_SESSION['pending_2fa_user_id'])) {
    die("Unauthorized access.");
}

$userId = $_SESSION['pending_2fa_user_id'];

// Fetch user's secret
$stmt = $conn->prepare("SELECT totp_secret FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($secret);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $remember = isset($_POST['remember']);

    $g = new PHPGangsta_GoogleAuthenticator();
    $isValid = $g->verifyCode($secret, $code, 2);

    if ($isValid) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['2fa_verified'] = true;
        unset($_SESSION['pending_2fa_user_id']);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $stmt = $conn->prepare("INSERT INTO trusted_devices (user_id, token_hash, expires_at, user_agent) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userId, $tokenHash, $expiresAt, $userAgent);
            $stmt->execute();
            $stmt->close();

            // Set cookie for 7 days
            setcookie('remember_2fa', $token, time() + (86400 * 7), "/", "", false, true);
        }

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid code. Please try again.";
    }
}
?>

<form method="POST">
    <h2>Enter 2FA Code</h2>
    <input type="text" name="code" required placeholder="6-digit code" />
    <br>
    <label><input type="checkbox" name="remember"> Remember this device for 7 days</label>
    <br><br>
    <button type="submit">Verify</button>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>
