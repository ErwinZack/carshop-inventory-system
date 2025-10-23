<?php
session_start();
include("../config/db.php");

// Only role_id 2 (staff/user) can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

// Fetch sales history (only active transactions)
$salesQuery = $conn->query("
    SELECT t.id, t.customer_name, t.customer_contact, t.customer_address,
           p.name AS product_name, t.quantity, t.price_per_unit, t.quantity * t.price_per_unit AS total,
           t.created_at
    FROM transactions t
    JOIN products p ON t.product_id = p.id
    WHERE t.status = 'active'
    ORDER BY t.created_at DESC
");

?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-receipt"></i> Sales History</h2>

    <table class="inventory-table">
        <thead>
            <tr>
                <!-- <th>Transaction ID</th> -->
                <th>Customer Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price/unit</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($salesQuery->num_rows > 0): ?>
            <?php while ($sale = $salesQuery->fetch_assoc()): ?>
            <tr>
                <!-- <td><?php echo $sale['id']; ?></td> -->
                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($sale['customer_contact']); ?></td>
                <td><?php echo htmlspecialchars($sale['customer_address']); ?></td>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                <td><?php echo $sale['quantity']; ?></td>
                <td>₱<?php echo number_format($sale['price_per_unit'], 2); ?></td>
                <td>₱<?php echo number_format($sale['total'], 2); ?></td>
                <td><?php echo date("M d, Y H:i", strtotime($sale['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" style="text-align:center;">No transactions recorded yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>