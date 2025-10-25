<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
include("../config/db.php");

// Define low stock threshold
$lowStockThreshold = 5;

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
    ";
} else {
    $query = "
        SELECT id, name, category, quantity, price 
        FROM products 
        WHERE quantity <= $lowStockThreshold
        ORDER BY quantity ASC
    ";
}

$result = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>
<?php include '../includes/getSearchAction.php'; ?>

<form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off">

<form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off">
    <?php if ($current_page === 'category.php' && isset($_GET['cat'])): ?>
        <input type="hidden" name="cat" value="<?php echo htmlspecialchars($_GET['cat']); ?>">
    <?php endif; ?>

    <input type="text" id="searchInput" name="q" placeholder="Search products..."
        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
        style="padding-right: 35px;">

</form>

<!-- Main Content -->
<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts</h2>

    <div class="page-search">
        <form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off" style="position: relative;">
            <?php if (basename($_SERVER['PHP_SELF']) === 'category.php' && isset($_GET['cat'])): ?>
                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($_GET['cat']); ?>">
            <?php endif; ?>

            <input type="text" id="searchInput" name="q" placeholder="Search products..."
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                style="padding-right: 35px;">

            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                viewBox="0 0 24 24"
                style="position: absolute; left: 320px; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <path
                    d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
            </svg>
        </form>

        <div id="searchResults" class="search-results"></div>
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
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td class="low-stock"><?php echo $row['quantity']; ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No products are currently low in stock.</p>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>

<!-- Clear navbar search input on back/forward navigation -->
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]'); // Navbar search input
        if (searchInput) {
            searchInput.value = ''; // Clear the search field
        }
        window.location.href = window.location.pathname; // Reload page without query string
    }
});
</script>