<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require '../config/db_connect.php';

$clientID     = "958716765056-9ud5d99ekd90bs8u88c1tm0fmfc1fh5j.apps.googleusercontent.com";
$clientSecret = "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";
$redirectUri  = "http://localhost/airbnb_wallet/public/google_register_callback.php";

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
        
        // Use null for phone if not provided by Google.
        $googlePhone = (isset($userInfo->phone) && !empty($userInfo->phone))
                        ? $userInfo->phone 
                        : null;
        
        try {
            // Connect to the database.
            $db = new PDO('mysql:host=localhost;dbname=airbnb_wallet', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database connection error: " . $e->getMessage();
            header("Location: login.php");
            exit;
        }
        
        // Check if a user with this email already exists.
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt->execute(['email' => $googleEmail]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            // If the email exists, prompt the user to log in instead.
            $_SESSION['error'] = "A user with that email already exists. Please log in.";
            header("Location: register.php");
            exit;
        } else {
            // Create a new user record.
            $dummyPass = password_hash('google_oauth_user', PASSWORD_DEFAULT);
            $stmtInsert = $db->prepare("
                INSERT INTO users (full_name, email, google_id, phone, password_hash)
                VALUES (:uname, :email, :gid, :phone, :pass)
            ");
            $stmtInsert->execute([
                'uname' => $googleName,
                'email' => $googleEmail,
                'gid'   => $googleId,
                'phone' => $googlePhone,
                'pass'  => $dummyPass
            ]);
            $_SESSION['success'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error retrieving token: " . $token["error"];
        header("Location: register.php");
        exit;
    }
} else {
    $_SESSION['error'] = "No code parameter provided.";
    header("Location: register.php");
    exit;
}
?>
