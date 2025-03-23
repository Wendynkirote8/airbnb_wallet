<?php
session_start();
require_once '.../config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate inputs.
    $username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
    $email    = trim(filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL));
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!$email) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists.
        $stmt = $pdo->prepare("SELECT admin_id FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "Username or email already exists. Please choose another.";
        } else {
            // Create a secure password hash.
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $password_hash]);
                $success = "Admin registered successfully. You can now log in.";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .register-container {
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
    <div class="register-container">
        <h2 class="text-center">Admin Registration</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="admin_register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Re-enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="mt-3 text-center">
                Already have an account? <a href="admin_login.php">Login here</a>.
            </p>
        </form>
    </div>
</body>
</html>
