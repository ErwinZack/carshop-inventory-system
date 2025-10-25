<?php
session_start();
require_once '../config/db.php';

// Only role_id 2 (staff/user) can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Fetch sales history
$query = "
    SELECT 
        t.customer_name,
        t.customer_contact,
        t.customer_address,
        GROUP_CONCAT(CONCAT(p.name, ' (x', t.quantity, ')') SEPARATOR ', ') AS products,
        SUM(t.quantity * t.price_per_unit) AS total_amount,
        t.created_at
    FROM transactions t
    JOIN products p ON t.product_id = p.id
    WHERE t.status = 'active'
    GROUP BY t.customer_name, t.customer_contact, t.customer_address, DATE(t.created_at)
    ORDER BY t.created_at DESC
";
$sales = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-receipt"></i> Sales History</h2>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Products</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sales->num_rows > 0): ?>
                <?php while ($sale = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($sale['customer_contact']); ?></td>
                        <td><?php echo htmlspecialchars($sale['customer_address']); ?></td>
                        <td><?php echo htmlspecialchars($sale['products']); ?></td>
                        <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                        <td><?php echo date("M d, Y H:i", strtotime($sale['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No transactions recorded yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>