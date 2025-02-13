<?php include '../includes/navbar.php'; ?>

<div class="deposit-container">
            <h2>Withdraw Funds</h2>
            <form action="../scripts/withdraw.php" method="POST" class="deposit-form">
            <input type="number" name="amount" placeholder="Amount" required>
            <button type="submit">Withdraw</button>
            </form>
        </div>
<?php include '../includes/navbarroot.php'; ?>