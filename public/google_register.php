<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
require '../config/db_connect.php';
require_once '../vendor/autoload.php';

$clientID     = "958716765056-9ud5d99ekd90bs8u88c1tm0fmfc1fh5j.apps.googleusercontent.com";
$clientSecret = "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";
$redirectUri  = "http://localhost/airbnb_wallet/public/google_register_callback.php";

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

$authUrl = $client->createAuthUrl();
header("Location: $authUrl");
exit;
?>
