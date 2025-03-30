<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/db_connect.php'; // Ensure the file creates a PDO instance named $pdo

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Generate a CSRF token if one isn't already present.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

// Handle form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify the CSRF token.
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        // Retrieve and validate form inputs.
        $user_id         = $_POST["user_id"] ?? '';
        $new_password    = $_POST["new_password"] ?? '';
        $confirm_password= $_POST["confirm_password"] ?? '';
        
        if (empty($user_id)) {
            $error = "No user selected.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($new_password) < 8) { // Enforce a minimum password length.
            $error = "Password must be at least 8 characters long.";
        } else {
            // Hash the new password and update the user record.
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id");
                $stmt->bindParam(':password_hash', $password_hash);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $success = "Password updated successfully for user ID: " . htmlspecialchars($user_id);
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch list of users for selection.
try {
    $stmt = $pdo->query("SELECT user_id, full_name, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Change User Password</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optionally include a chart library like Chart.js for the Transaction Trends -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
      overflow-x: hidden;
    }
    /* Sidebar styling */
    #sidebar-wrapper {
      width: 250px;
      min-height: 100vh;
      background-color: #fff;
      border-right: 1px solid #dee2e6;
    }
    .sidebar-heading {
      font-size: 1.2rem;
      font-weight: bold;
      padding: 1rem;
      background-color: #e9ecef;
      margin-bottom: 0;
    }
    .list-group-item {
      border: none;
      border-bottom: 1px solid #dee2e6;
    }
    .list-group-item:hover {
      background-color: #f0f0f0;
      cursor: pointer;
    }
    /* Page content wrapper */
    #page-content-wrapper {
      flex: 1;
      width: 100%;
    }
    /* Top navbar styling */
    .top-navbar {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
    }
    .top-navbar .navbar-brand {
      font-weight: 600;
    }
    /* Card styling for stats boxes */
    .stats-card {
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 6px;
      padding: 1rem;
      text-align: center;
    }
    .stats-card h5 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: bold;
    }
    .stats-card p {
      margin: 0;
      font-size: 0.9rem;
      color: #6c757d;
    }
  </style>
</head>
<body>

<div class="d-flex">
<?php include '../includes/navbar.php'; ?>
  </div>

  <!-- Page Content -->
  <div id="page-content-wrapper">
    <!-- Top Navbar -->
    <nav class="navbar top-navbar">
      <div class="container-fluid">
        
        <div>
          <!-- You can place a user avatar or logout button here -->
          <span class="me-3">Back</span>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-3">
      <!-- Stats Row -->
      

      <!-- Transaction Trends Section -->
      <div class="row mt-4">
        
        </div>
      </div>

      <!-- Change User Password Form -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Change User Password</h5>
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
              <?php endif; ?>

              <div class="mb-3">
                <label for="userSelect" class="form-label">Select User</label>
                <select id="userSelect" class="form-select" onchange="populateUserId(this)">
                  <option value="">-- Select User --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['user_id']; ?>">
                      <?php echo htmlspecialchars($user['full_name']) . ' (' . htmlspecialchars($user['email']) . ')'; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <form method="POST" action="admin_change_password.php">
                <input type="hidden" name="user_id" id="user_id">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="mb-3">
                  <label for="new_password" class="form-label">New Password</label>
                  <input type="password" name="new_password" class="form-control" id="new_password" required minlength="8">
                </div>
                <div class="mb-3">
                  <label for="confirm_password" class="form-label">Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" id="confirm_password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- container-fluid -->
  </div> <!-- page-content-wrapper -->
</div> <!-- d-flex -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  function populateUserId(selectElement) {
    document.getElementById('user_id').value = selectElement.value;
  }

  // Example Chart.js script to display a simple line chart for Transaction Trends
  const ctx = document.getElementById('transactionChart').getContext('2d');
  const transactionChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
      datasets: [{
        label: 'Deposits/Withdrawals',
        data: [5, 10, 7, 12, 8],
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1,
        fill: false
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

</body>
</html>
