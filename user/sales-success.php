<?php
// admin/sales-success.php
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: ../login.php');

$status = $_GET['status'] ?? '';
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Sale Result</title></head><body>
<?php include '../partials/sidebar.php'; ?>
<main class="container">
  <?php if ($status === 'ok'): ?>
    <h2>Sale completed successfully!</h2>
    <p><a href="add-sales.php">Make another sale</a> | <a href="sales-history.php">View Sales History</a></p>
  <?php else: ?>
    <h2>Sale not completed</h2>
    <p><a href="add-sales.php">Back</a></p>
  <?php endif; ?>
</main>
</body></html>
