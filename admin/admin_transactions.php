<?php
session_start();
require_once '../config/db_connect.php';

// Ensure only admins can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where = "";
$params = [];
if ($q !== '') {
    $where = " WHERE transaction_id LIKE :q OR wallet_id LIKE :q OR transaction_type LIKE :q OR status LIKE :q";
    $params[':q'] = '%' . $q . '%';
}

// Get total rows for pagination
$totalQuery = "SELECT COUNT(*) FROM transactions" . $where;
$stmt = $pdo->prepare($totalQuery);
$stmt->execute($params);
$totalRows = $stmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch transactions with the applied search and pagination
$sql = "SELECT transaction_id, wallet_id, amount, transaction_type, status, created_at 
        FROM transactions" . $where . " 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Transactions</title>
  <link rel="stylesheet" href="../assets/css/admin_style.css">
  <!-- Ionicons for icons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <style>
    /* Additional styling for pagination */
    .pagination {
      margin-top: 15px;
      display: flex;
      justify-content: center;
      gap: 15px;
      align-items: center;
    }
    .pagination a {
      text-decoration: none;
      padding: 5px 10px;
      background: var(--blue);
      color: var(--white);
      border-radius: 4px;
      transition: background 0.3s;
    }
    .pagination a:hover {
      background: var(--blue2);
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Navigation Sidebar -->
    <div class="navigation">
      <?php include '../includes/navbar_admin.php'; ?>
    </div>
    <!-- Main Content Area -->
    <div class="main">
      <!-- Topbar with Search Bar -->
      <div class="topbar">
        <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
        <div class="search">
          <form method="GET" action="admin_transactions.php">
            <input type="text" name="q" placeholder="Search transactions" value="<?php echo htmlspecialchars($q); ?>">
          </form>
        </div>
        <div class="user">
          <img src="../assets/imgs/default-profile.png" alt="Admin Profile">
        </div>
      </div>
      <!-- Content -->
      <div class="content">
        <h2>Transactions</h2>
        <?php if (count($transactions) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>Wallet ID</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($transactions as $tx): ?>
              <tr>
                <td><?php echo htmlspecialchars($tx['transaction_id']); ?></td>
                <td><?php echo htmlspecialchars($tx['wallet_id']); ?></td>
                <td><?php echo htmlspecialchars($tx['amount']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($tx['transaction_type'])); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($tx['status'])); ?></td>
                <td><?php echo htmlspecialchars($tx['created_at']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- Pagination -->
          <div class="pagination">
            <?php if ($page > 1): ?>
              <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Previous</a>
            <?php endif; ?>
            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages): ?>
              <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <p>No transactions found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
