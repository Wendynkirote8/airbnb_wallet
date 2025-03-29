<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
require '../config/db_connect.php';

use PHPGangsta_GoogleAuthenticator;

// Ensure the user is logged in or is setting up 2FA
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die("You must be logged in to enable 2FA.");
}

$ga = new PHPGangsta_GoogleAuthenticator();

// Generate a new secret
$secret = $ga->createSecret();

// Create the QR code URL
// Change "MyWebsite" to your site/app name
$qrCodeUrl = $ga->getQRCodeGoogleUrl('MyWebsite', $secret);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Enable 2FA</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; padding: 2rem; }
    img { border: 1px solid #ddd; }
  </style>
</head>
<body>
  <h1>Enable Two-Factor Authentication (2FA)</h1>
  <p>Scan this QR code with your authenticator app:</p>
  <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" />
  <p>Or manually enter the secret: <strong><?php echo $secret; ?></strong></p>
  <p>Once set up, you’ll be asked for a 6-digit code at login.</p>
</body>
</html>