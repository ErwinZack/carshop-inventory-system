<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage-inventory.php");
    exit();
}

$id = (int)$_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);

    $image = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // ✅ Allow WEBP also
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image = "uploads/" . $fileName;
            } else {
                $error = "Error uploading image. Check folder permissions.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, GIF, & WEBP files are allowed.";
        }
    }

    if (empty($error)) {
        $sql = "UPDATE products 
                SET name='$name', category='$category', quantity=$quantity, price=$price, description='$description', image='$image' 
                WHERE id=$id";
        if ($conn->query($sql)) {
            header("Location: manage-inventory.php");
            exit();
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content edit-product-page">
    <h2 class="page-title">Edit Product</h2>

    <form method="POST" enctype="multipart/form-data" class="edit-product-form">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="" disabled>Select Category</option>
                <?php
        // List of categories
        $categories = ['Oil', 'Lubricants', 'Tires', 'Accessories', 'Auto Parts', 'Car Parts', 'Vehicles'];
        foreach ($categories as $cat):
            $selected = ($product['category'] === $cat) ? 'selected' : '';
        ?>
                <option value="<?php echo $cat; ?>" <?php echo $selected; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="<?php echo $product['quantity']; ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Price (₱):</label>
            <input type="number" step="0.01" name="price" id="price" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required
                style="width: 482px; height: 87px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <?php if (!empty($product['image'])): ?>
        <div class="form-group current-image">
            <label>Current Image:</label>
            <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image"
                class="product-preview">
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="image">Upload New Image:</label>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>

        <?php if (!empty($error)): ?>
        <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-save">Update Product</button>
            <a href="manage-inventory.php" class="btn btn-back">← Back to Inventory</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>