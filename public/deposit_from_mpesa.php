<?php
session_start();
require '../config/db_connect.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the user is authenticated
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

// M-Pesa Daraja API credentials (Sandbox example)
$mpesaConsumerKey    = 'your_consumer_key';
$mpesaConsumerSecret = 'your_consumer_secret';
$mpesaShortCode      = 'your_shortcode';    // e.g., "174379" in sandbox
$mpesaPasskey        = 'your_passkey';
$mpesaApiUrl         = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$successMessage = $errorMessage = "";

/**
 * Get M-Pesa access token
 */
function getMpesaAccessToken($consumerKey, $consumerSecret) {
    $credentials = base64_encode($consumerKey . ":" . $consumerSecret);
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    if($result === false){
        die("Curl error: " . curl_error($curl));
    }
    curl_close($curl);
    $result = json_decode($result);
    return $result->access_token;
}

/**
 * Initiate an M-Pesa STK Push
 */
function initiateMpesaSTKPush($amount, $phoneNumber, $accessToken, $mpesaShortCode, $mpesaPasskey, $callbackUrl, $mpesaApiUrl) {
    $timestamp = date("YmdHis");
    $password = base64_encode($mpesaShortCode . $mpesaPasskey . $timestamp);
    $data = array(
        "BusinessShortCode" => $mpesaShortCode,
        "Password"          => $password,
        "Timestamp"         => $timestamp,
        "TransactionType"   => "CustomerPayBillOnline",
        "Amount"            => $amount,
        "PartyA"            => $phoneNumber,
        "PartyB"            => $mpesaShortCode,
        "PhoneNumber"       => $phoneNumber,
        "CallBackURL"       => $callbackUrl,
        "AccountReference"  => "YourCompany", // Reference shown on the phone prompt
        "TransactionDesc"   => "Deposit funds"
    );
    $jsonData = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $mpesaApiUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    $response = curl_exec($curl);
    if($response === false) {
        die("Curl error: " . curl_error($curl));
    }
    curl_close($curl);
    return json_decode($response);
}

// Handle the deposit form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount      = floatval($_POST["amount"]);
    $phoneNumber = trim($_POST["phoneNumber"]);

    // Validate minimum deposit amount
    if ($amount < 10) {
        $errorMessage = "Minimum deposit is Ksh 10.";
    } else {
        // Obtain an access token from M-Pesa
        $accessToken = getMpesaAccessToken($mpesaConsumerKey, $mpesaConsumerSecret);
        // Set your callback URL (this should be a publicly accessible endpoint to process M-Pesa responses)
        $callbackUrl = "https://yourdomain.com/mpesa_callback.php"; // Update with your callback URL

        // Initiate the STK Push
        $response = initiateMpesaSTKPush($amount, $phoneNumber, $accessToken, $mpesaShortCode, $mpesaPasskey, $callbackUrl, $mpesaApiUrl);

        // Check the response from M-Pesa
        if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
            $successMessage = "STK Push initiated successfully. Please check your phone to complete the payment.";
        } else {
            $errorMessage = "Error initiating STK Push: " . (isset($response->errorMessage) ? $response->errorMessage : "Unknown error");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Deposit via M-Pesa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Add your CSS files or inline styles here -->
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .form-container { max-width: 400px; margin: 0 auto; }
    .message { padding: 10px; margin-bottom: 20px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Deposit via M-Pesa</h1>
    <?php if (!empty($successMessage)): ?>
      <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php elseif (!empty($errorMessage)): ?>
      <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>
    <form action="deposit_from_mpesa.php" method="POST">
      <label for="amount">Amount (Ksh):</label>
      <input type="number" name="amount" id="amount" required min="10" step="0.01">
      <br><br>
      <label for="phoneNumber">Phone Number (e.g., 2547XXXXXXXX):</label>
      <input type="text" name="phoneNumber" id="phoneNumber" required>
      <br><br>
      <button type="submit">Deposit</button>
    </form>
  </div>
</body>
</html>
