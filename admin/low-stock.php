<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
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

<!-- Main Content -->
<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts</h2>

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