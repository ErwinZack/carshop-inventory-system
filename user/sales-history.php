<?php
session_start();
require_once '../config/db.php';

// Only role_id 2 (staff/user) can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

// Handle search query
$search = '';
$searchSQL = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $searchSQL = "AND (
        t.customer_name LIKE '%$search%' OR 
        t.customer_contact LIKE '%$search%' OR 
        t.customer_address LIKE '%$search%' OR 
        p.name LIKE '%$search%'
    )";
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
    WHERE t.status = 'active' $searchSQL
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

    <!-- Search Filter -->
    <div class="page-search" style="margin-bottom: 15px; text-align: right;">
        <form method="GET" autocomplete="off" style="position: relative; display: inline-block;">
            <input type="text" id="searchInput" name="q" placeholder="Search by customer or product..."
                value="<?php echo htmlspecialchars($search); ?>" style="padding-right: 35px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <path
                    d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
            </svg>
        </form>
    </div>

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
                <td>₱<?php echo number_format($sale['total_amount'], 0); ?></td>
                <td><?php echo date("M d, Y H:i", strtotime($sale['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No transactions found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>

<style>
.page-search {
    margin-bottom: 20px;
    text-align: right;
}

#searchInput {
    width: 250px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    padding: 8px 35px 8px 12px;
    font-size: 14px;
    color: #333;
    transition: 0.2s;
    /* margin-left: 1100px; */
}

#searchInput:focus {
    border-color: #2980b9;
    box-shadow: 0 0 4px rgba(41, 128, 185, 0.3);
    outline: none;
}

.inventory-table tbody tr:hover {
    background-color: #eef5ff !important;
    cursor: pointer;
    box-shadow: inset 4px 0 0 #2980b9;
}

.inventory-table tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>

<script>
// Clear search input on back navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('#searchInput');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
    }
});
</script>