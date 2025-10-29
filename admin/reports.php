<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Example: Total stock per category
$result = $conn->query("SELECT category, SUM(quantity) as total_quantity FROM products GROUP BY category");
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Reports & Analytics</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>Category</th>
            <th>Total Quantity</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo $row['total_quantity']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>