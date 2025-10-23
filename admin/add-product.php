<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $added_by = $_SESSION['user_id']; // 👈 current admin ID from session

    // Default image path
    $image = '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image = "uploads/" . $fileName;
            } else {
                $error = "Error uploading image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, GIF, & WEBP files are allowed.";
        }
    }

    // ✅ Insert into database (now includes added_by)
    if (empty($error)) {
        $sql = "INSERT INTO products (name, category, quantity, price, description, image, added_by)
                VALUES ('$name', '$category', $quantity, $price, '$description', '$image', $added_by)";
        
        if ($conn->query($sql)) {
            header("Location: manage-inventory.php");
            exit();
        } else {
            $error = "Failed to add product: " . $conn->error;
        }
    }
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content add-product-page">
    <h2 class="page-title">Add New Product</h2>

    <form method="POST" enctype="multipart/form-data" class="add-product-form">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" name="category" id="category" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" required>
        </div>

        <div class="form-group">
            <label for="price">Price (₱):</label>
            <input type="number" step="0.01" name="price" id="price" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Upload Image:</label>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>

        <?php if (!empty($error)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-save">Add Product</button>
            <a href="manage-inventory.php" class="btn btn-back">← Back to Inventory</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>