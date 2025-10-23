<?php
session_start();
include("../config/db.php");

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

// Example: Fetch products with low stock (threshold: <5)
$result = $conn->query("SELECT * FROM products WHERE quantity < 5 ORDER BY name ASC");

?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Notifications</h2>

    <?php if ($result->num_rows == 0): ?>
    <p>No notifications. All products have sufficient stock.</p>
    <?php else: ?>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <strong><?php echo htmlspecialchars($row['name']); ?></strong> – Only
            <?php echo $row['quantity']; ?> left in stock!
        </li>
        <?php endwhile; ?>
    </ul>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>