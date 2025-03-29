<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require '../config/db_connect.php';

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
    // Exchange the authorization code for an access token.
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        
        // Get user profile information.
        $oauth2   = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        
        $googleId    = $userInfo->id;
        $googleEmail = $userInfo->email;
        $googleName  = $userInfo->name;
        
        try {
            // Connect to the database.
            $db = new PDO('mysql:host=localhost;dbname=airbnb_wallet', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
        
        // Check if a user with this email exists.
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $googleEmail]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            // If google_id isn't stored, update it.
            if (empty($existingUser['google_id'])) {
                $updateStmt = $db->prepare("UPDATE users SET google_id = :gid WHERE user_id = :uid");
                $updateStmt->execute(['gid' => $googleId, 'uid' => $existingUser['user_id']]);
            }
            // Check if MFA is enabled.
            if (!empty($existingUser['totp_secret'])) {
                $_SESSION['pending_2fa_user_id'] = $existingUser['user_id'];
                header("Location: verify_2fa.php");
                exit;
            } else {
                $_SESSION['user_id'] = $existingUser['user_id'];
            }
        } else {
            // User not found: prompt to register.
            $_SESSION['error'] = "User with that Google account not found. Please register.";
            header("Location: login.php");
            exit;
        }
        
        // Redirect to the dashboard after successful login.
        header("Location: dashboard.php");
        exit;
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
?>
