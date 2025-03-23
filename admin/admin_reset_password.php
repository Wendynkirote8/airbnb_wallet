<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/db_connect.php';

$success = "";
$error = "";

// e.g. user visits: admin_reset_password.php?token=abcd1234
if (!isset($_GET['token'])) {
    die("Invalid token.");
}
$token = $_GET['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        try {
            // 1. Check if token is valid
            $stmt = $pdo->prepare("
                SELECT admin_id 
                FROM admin_password_resets 
                WHERE token = :token 
                  AND created_at > (NOW() - INTERVAL 1 HOUR)
            ");
            $stmt->execute([':token' => $token]);
            $resetRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resetRow) {
                $adminId = $resetRow['admin_id'];

                // 2. Update admin's password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET password_hash = :hash WHERE admin_id = :admin_id");
                $stmt->execute([':hash' => $hashedPassword, ':admin_id' => $adminId]);

                // 3. Delete the reset token (optional)
                $stmt = $pdo->prepare("DELETE FROM admin_password_resets WHERE admin_id = :admin_id");
                $stmt->execute([':admin_id' => $adminId]);

                $success = "Password has been updated successfully. You can now login.";
            } else {
                $error = "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 500px; margin-top: 50px;">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center">Reset Password</h2>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($success)): ?>
            <form action="admin_reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input 
                      type="password" 
                      class="form-control" 
                      id="new_password" 
                      name="new_password" 
                      placeholder="Enter new password" 
                      required
                    >
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input 
                      type="password" 
                      class="form-control" 
                      id="confirm_password" 
                      name="confirm_password" 
                      placeholder="Confirm new password" 
                      required
                    >
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
            <?php endif; ?>

            <div class="mt-3 text-center">
                <a href="admin_login.php">Back to Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
