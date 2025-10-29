<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

$flash_message = '';
$flash_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $added_by = $_SESSION['user_id'];

    $image = '';
    $error = '';

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

    // Insert into DB
    if (empty($error)) {
        $sql = "INSERT INTO products (name, category, quantity, price, description, image, added_by)
                VALUES ('$name', '$category', $quantity, $price, '$description', '$image', $added_by)";
        
        if ($conn->query($sql)) {
            $flash_message = "✅ Product added successfully!";
            $flash_type = "success";
        } else {
            $flash_message = "❌ Failed to add product. Please try again.";
            $flash_type = "error";
        }
    } else {
        $flash_message = "⚠️ " . $error;
        $flash_type = "error";
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
            <select name="category" id="category" required>
                <option value="" disabled selected>Select Category</option>
                <option value="Oil">Oil</option>
                <option value="Lubricants">Lubricants</option>
                <option value="Tires">Tires</option>
                <option value="Accessories">Accessories</option>
                <option value="Auto parts">Auto Parts</option>
                <option value="Car parts">Car Parts</option>
                <option value="Vehicles">Vehicles</option>
            </select>
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

        <div class="form-actions">
            <button type="submit" class="btn btn-save">Add Product</button>
            <a href="manage-inventory.php" class="btn btn-back">← Back to Inventory</a>
        </div>
    </form>

    <!-- ✅ Flash Message (specific to Add Product page) -->
    <?php if (!empty($flash_message)): ?>
    <div class="add-product-flash-message <?php echo $flash_type; ?>">
        <?php echo htmlspecialchars($flash_message); ?>
    </div>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>

<script>
// Auto fade-out for flash message
setTimeout(() => {
    const msg = document.querySelector('.add-product-flash-message');
    if (msg) {
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 500);
    }
}, 2500);
</script>

<style>
/* ✅ Scoped styling for Add Product page only */
.add-product-page {
    position: relative;
}

.add-product-form {
    position: relative;
}

/* ✅ Flash message directly below the Add Product form */
.add-product-flash-message {
    display: block;
    margin-top: 18px;
    /* space below form buttons */
    padding: 10px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    width: 39%;
    /* same width as form */
    box-sizing: border-box;
    transition: opacity 0.5s ease;
}

/* ✅ Success Message Style */
.add-product-flash-message.success {
    background-color: #e8f8ed;
    color: #1d6430;
    border-left: 4px solid #28a745;
}

/* ✅ Error Message Style */
.add-product-flash-message.error {
    background-color: #fdebec;
    color: #721c24;
    border-left: 4px solid #dc3545;
}
</style>