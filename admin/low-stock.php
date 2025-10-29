<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
include("../config/db.php");
include("../includes/getSearchAction.php");

// Define low stock threshold
$lowStockThreshold = 10;

// Handle search query from navbar
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $query = "
        SELECT id, name, category, quantity, price 
        FROM products 
        WHERE quantity <= $lowStockThreshold
        AND (name LIKE '%$search%' OR category LIKE '%$search%')
        ORDER BY quantity ASC
        LIMIT 10
    ";
} else {
    $query = "
        SELECT id, name, category, quantity, price 
        FROM products 
        WHERE quantity <= $lowStockThreshold
        ORDER BY quantity ASC
        LIMIT 10
    ";
}

$result = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content inventory-page">
    <h2 class="page-title">
        <i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts
    </h2>

    <div class="page-search" style="margin-bottom: 15px; text-align: right;">
        <form action="<?php echo getSearchAction($_SESSION['role_id']); ?>" method="GET" autocomplete="off"
            style="position: relative; display: inline-block;">
            <input type="text" id="searchInput" name="q" placeholder="Search products..."
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                style="padding-right: 35px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <path
                    d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
            </svg>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?php echo ($row['quantity'] <= $lowStockThreshold) ? 'low-stock' : ''; ?>">
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align:center;">No products are currently low in stock.</p>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>

<style>
/* Match user low-stock page styling */
.page-title {
    margin-bottom: 10px;
}

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
}

#searchInput:focus {
    border-color: #2980b9;
    box-shadow: 0 0 4px rgba(41, 128, 185, 0.3);
    outline: none;
}

/* Low stock rows highlighted in light red */
.inventory-table tr.low-stock {
    background-color: #fdecea;
    color: #c0392b;
}

/* Hover effect for rows */
.inventory-table tbody tr:hover {
    background-color: #eef5ff !important;
    cursor: pointer;
    box-shadow: inset 4px 0 0 #2980b9;
}

/* Alternate row background */
.inventory-table tr:nth-child(even):not(.low-stock) {
    background-color: #f9f9f9;
}
</style>

<script>
// Clear navbar search input on back/forward navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
    }
});
</script>