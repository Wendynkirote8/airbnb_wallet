<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

// Google client setup
$clientID     = "958716765056-9ud5d99ekd90bs8u88c1tm0fmfc1fh5j.apps.googleusercontent.com";
$clientSecret = "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";
$redirectUri  = "http://localhost/airbnb_wallet/public/google_callback.php";

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);

        // Get user info from Google
        $oauth2   = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $googleId    = $userInfo->id;
        $googleEmail = $userInfo->email;
        $googleName  = $userInfo->name;

        // Connect to DB
        try {
            $db = new PDO('mysql:host=localhost;dbname=airbnb_wallet', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        // Check if user exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $googleEmail]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // Update google_id if missing
            if (empty($existingUser['google_id'])) {
                $updateStmt = $db->prepare("UPDATE users SET google_id = :gid WHERE user_id = :uid");
                $updateStmt->execute([
                    'gid' => $googleId,
                    'uid' => $existingUser['user_id']
                ]);
            }

            // Check if device is remembered
        if (!empty($_COOKIE['remember_2fa'])) {
            $token = $_COOKIE['remember_2fa'];
            $tokenHash = hash('sha256', $token);
            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $stmt = $db->prepare("SELECT * FROM trusted_devices WHERE user_id = :uid AND token_hash = :hash AND expires_at > NOW() AND user_agent = :ua");
            $stmt->execute([
                'uid' => $existingUser['user_id'],
                'hash' => $tokenHash,
                'ua' => $userAgent
            ]);
            $trusted = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($trusted) {
                $_SESSION['user_id'] = $existingUser['user_id'];
                $_SESSION['2fa_verified'] = true;
                header("Location: dashboard.php");
                exit;
            }
        }


            // ✅ ALWAYS force 2FA check on every login
            if (!empty($existingUser['totp_secret'])) {
                $_SESSION['pending_2fa_user_id'] = $existingUser['user_id'];
                unset($_SESSION['user_id']);          // Clear old login
                unset($_SESSION['2fa_verified']);     // Clear 2FA flag
                header("Location: verify_2fa.php");
                exit;
            } else {
                // No 2FA: log them in
                $_SESSION['user_id'] = $existingUser['user_id'];
                $_SESSION['2fa_verified'] = true;
                header("Location: dashboard.php");
                exit;
            }
        } else {
            // User not found
            $_SESSION['error'] = "User with that Google account not found. Please register.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error retrieving token: " . $token["error"];
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "No code parameter provided.";
    header("Location: login.php");
    exit;
}
