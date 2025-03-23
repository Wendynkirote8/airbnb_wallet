<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/db_connect.php';

$error = ""; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
    $password = $_POST["password"];

    try {
        // Query the admins table for a matching username.
        $stmt = $pdo->prepare("SELECT admin_id, username, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Use password_verify to compare the submitted password with the stored hash.
        if ($admin && password_verify($password, $admin["password_hash"])) {
            $_SESSION["admin_id"] = $admin["admin_id"];
            $_SESSION["admin_username"] = $admin["username"];
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Disable autocomplete for the form -->
        <form action="admin_login.php" method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input 
                  type="text" 
                  name="username" 
                  class="form-control" 
                  id="username" 
                  placeholder="Enter your username" 
                  autocomplete="off" 
                  required
                >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                  type="password" 
                  name="password" 
                  class="form-control" 
                  id="password" 
                  placeholder="Enter your password" 
                  autocomplete="new-password" 
                  required
                >
            </div>

            <!-- Forgot Password Link -->
            <div class="d-flex justify-content-between mb-3">
                <a href="admin_forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
            <a href="../public/login.php" class="btn btn-secondary">Login as normal user</a>
        </div>
    </div>
</body>
</html>
