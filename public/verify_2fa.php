<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require '../config/db_connect.php';

use OTPHP\TOTP;

$error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['pending_2fa_user_id'] ?? null;
    if (!$userId) {
        die("No user pending 2FA verification.");
    }

    $code = trim($_POST['totp_code'] ?? '');

    try {
        // Connect to the database
        $db = new PDO('mysql:host=localhost;dbname=airbnb_wallet', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve the user's TOTP secret from the database
        $stmt = $db->prepare("SELECT totp_secret FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        $secret = $stmt->fetchColumn();

        if (!$secret) {
            die("TOTP secret not found. 2FA not enabled?");
        }

        // Create a TOTP instance with the stored secret
        $totp = TOTP::create($secret);
        $totp->setLabel('weshPAY');
        $totp->setParameter('issuer', 'weshPAY');

        // Verify the code. (By default, TOTP verifies the current time window.)
        if ($totp->verify($code)) {
            // Code is valid – complete login
            $_SESSION['user_id'] = $userId;
            unset($_SESSION['pending_2fa_user_id']);
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid code. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify Two-Factor Authentication</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      padding: 2rem;
      background-color: #f4f4f4;
    }
    .container {
      max-width: 400px;
      margin: 0 auto;
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .error {
      color: red;
      margin-bottom: 1rem;
    }
    input[type="text"] {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      background: #2a2185;
      color: #fff;
      border: none;
      padding: 0.8rem 1.2rem;
      font-size: 1rem;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background: #1f186b;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Two-Factor Authentication</h1>
    <?php if (!empty($error)) : ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
      <label for="totp_code">Enter your 6-digit code:</label>
      <input type="text" name="totp_code" id="totp_code" placeholder="123456" maxlength="6" required>
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
