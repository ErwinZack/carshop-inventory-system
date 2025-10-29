<?php
session_start();
include("../config/db.php");

// ✅ Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// ✅ Define low stock threshold
$lowStockThreshold = 10;

// ✅ Fetch all active products
$query = "SELECT * FROM products WHERE status = 'active' ORDER BY category ASC, name ASC";
$result = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content inventory-page">
    <h2 class="page-title">
        <i class="fa-solid fa-boxes-stacked"></i> Manage Inventory
    </h2>

    <!-- ✅ Toast Flash Message -->
    <?php if (isset($_SESSION['inventory_flash_success'])): ?>
    <div class="inventory-toast success show">
        <?php echo $_SESSION['inventory_flash_success']; unset($_SESSION['inventory_flash_success']); ?>
    </div>
    <?php elseif (isset($_SESSION['inventory_flash_error'])): ?>
    <div class="inventory-toast error show">
        <?php echo $_SESSION['inventory_flash_error']; unset($_SESSION['inventory_flash_error']); ?>
    </div>
    <?php endif; ?>

    <!-- Add Product Button -->
    <div class="inventory-header" style="margin-bottom: 15px;">
        <a href="add-product.php" class="btn btn-add">
            <i class="fa fa-plus"></i> Add Product
        </a>
    </div>

    <!-- Inventory Table -->
    <table class="inventory-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?php echo ($row['quantity'] <= $lowStockThreshold) ? 'low-stock' : ''; ?>">
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td class="action-buttons">
                    <a href="edit-product.php?id=<?php echo $row['id']; ?>" class="action-icon edit-icon" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <!-- Archive Product -->
                    <a href="archive-product.php?id=<?php echo $row['id']; ?>" class="action-icon archive-icon"
                        title="Archive Product"
                        onclick="return confirm('Are you sure you want to archive this product?');">
                        <i class="fa fa-archive"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>

<!-- ✅ Styles -->
<style>
/* Highlight low-stock rows */
.inventory-table tr.low-stock {
    background-color: #fdecea;
    color: #c0392b;
}

/* ✅ Toast Flash Message */
.inventory-toast {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #fff;
    padding: 14px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease;
    max-width: 300px;
    text-align: left;
    pointer-events: none;
}

/* Success Toast */
.inventory-toast.success {
    border-left: 5px solid #28a745;
    color: #28a745;
    background-color: #e8f9ee;
}

/* Error Toast */
.inventory-toast.error {
    border-left: 5px solid #d9534f;
    color: #d9534f;
    background-color: #fdeaea;
}

/* When visible */
.inventory-toast.show {
    opacity: 1;
    transform: translateY(0);
}
</style>

<!-- ✅ Toast Message Transition -->
<script>
window.addEventListener("DOMContentLoaded", () => {
    const toast = document.querySelector(".inventory-toast");
    if (toast) {
        // Fade in
        setTimeout(() => toast.classList.add("show"), 100);

        // Fade out after 3 seconds
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(20px)";
        }, 1000);

        // Fully remove after animation
        setTimeout(() => {
            toast.remove();
        }, 1600);
    }
});

// Optional: clear navbar search input when navigating back
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.href = window.location.pathname;
    }
});
</script>