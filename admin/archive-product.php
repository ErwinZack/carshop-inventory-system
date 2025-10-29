<?php
session_start();
include("../config/db.php");

// ✅ Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// ✅ Archive product if ID is provided
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    $stmt = $conn->prepare("UPDATE products SET status = 'archived' WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // ✅ Set flash message for success
        $_SESSION['inventory_flash_success'] = "✅ Product archived successfully!";
    } else {
        // ❌ Set flash message for error
        $_SESSION['inventory_flash_error'] = "❌ Failed to archive product. Please try again.";
    }

    $stmt->close();
}

// ✅ Redirect back to Manage Inventory page
header("Location: manage-inventory.php");
exit();
?>