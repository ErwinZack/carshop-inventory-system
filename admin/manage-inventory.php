<?php
session_start();
include("../config/db.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle search query from navbar
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $query = "
        SELECT * FROM products
        WHERE name LIKE '%$search%'
        OR category LIKE '%$search%'
        ORDER BY category ASC, name ASC
    ";
} else {
    $query = "SELECT * FROM products ORDER BY category ASC, name ASC";
}

$result = $conn->query($query);

?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content inventory-page">
    <h2 class="page-title">
        <i class="fa-solid fa-boxes-stacked"></i> Manage Inventory
    </h2>


    <div class="inventory-header">
        <a href="add-product.php" class="btn btn-add">
            <i class="fa fa-plus"></i> Add Product
        </a>
    </div>


    <table class="inventory-table">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <!-- <td><?php echo $row['id']; ?></td> -->
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td class="action-buttons">
                    <a href="edit-product.php?id=<?php echo $row['id']; ?>" class="action-icon edit-icon" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="delete-product.php?id=<?php echo $row['id']; ?>" class="action-icon delete-icon"
                        title="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>

            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>

<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
    }
});
</script>