<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: inventory.php");
    exit();
}

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>
<div class="main-content product-details-page">

    <h2 class="page-title">Product Details</h2>

    <div class="product-details-card">
        <?php if (!empty($product['image'])): ?>
        <div class="product-image">
            <img src="../<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-preview">
        </div>
        <?php endif; ?>

        <div class="product-info">
            <p><strong>Name:</strong> <span><?php echo htmlspecialchars($product['name']); ?></span></p>
            <p><strong>Category:</strong> <span><?php echo htmlspecialchars($product['category']); ?></span></p>
            <p><strong>Quantity:</strong> <span><?php echo $product['quantity']; ?></span></p>
            <p><strong>Price:</strong> <span>₱<?php echo number_format($product['price'], 0); ?></span></p>

            <?php if (!empty($product['description'])): ?>
            <p class="product-description"><strong>Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-actions">
        <!-- <a href="inventory.php" class="btn btn-back">← Back to Inventory</a> -->
    </div>
</div>



</div>


<?php include("../includes/footer.php"); ?>