<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch products with low stock
$result = $conn->query("SELECT * FROM products WHERE quantity < 5");
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Low Stock Notifications</h2>

    <?php if ($result->num_rows == 0): ?>
    <p>All products have sufficient stock.</p>
    <?php else: ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
        <li><?php echo htmlspecialchars($row['name']); ?> – Only <?php echo $row['quantity']; ?> left in stock!</li>
        <?php endwhile; ?>
    </ul>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>