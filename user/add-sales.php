<?php
require_once '../config/db.php';
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

// ✅ Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ✅ Fetch products
$stmt = $conn->prepare("SELECT id, name, category, quantity, price FROM products ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();

$oldSelected = $_POST['selected'] ?? [];
$oldQty = $_POST['qty'] ?? [];

?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>



<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa fa-cart-plus"></i> Add Sales</h2>


    <?php if ($result->num_rows === 0): ?>
    <p>No products found.</p>
    <?php else: ?>
    <form method="post" action="checkout.php" novalidate>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Available</th>
                    <th>Price (₱)</th>
                    <th>Sell Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $isChecked = in_array($row['id'], $oldSelected) ? 'checked' : ''; 
                    $quantityValue = isset($oldQty[$row['id']]) ? (int)$oldQty[$row['id']] : 1;
                    ?>
                <tr class="<?php echo ($row['quantity'] <= 10) ? 'low-stock-row' : ''; ?>">
                    <td>
                        <input type="checkbox" name="selected[]" value="<?php echo (int)$row['id']; ?>"
                            <?= $isChecked; ?>>
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo (int)$row['quantity']; ?></td>
                    <td>₱<?php echo number_format($row['price'], 0); ?></td>
                    <td>
                        <input type="number" min="1" name="qty[<?php echo (int)$row['id']; ?>]" class="qty-input"
                            value="<?php echo $quantityValue; ?>">
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
    </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Client-side validation to ensure at least one product is selected -->
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="selected[]"]:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert("Please select at least one product before proceeding.");
    }
});
</script>