<?php
session_start();
include("../config/db.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Restore product status to active
    $stmt = $conn->prepare("UPDATE products SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product restored successfully!";
    } else {
        $_SESSION['error'] = "Failed to restore product.";
    }

    $stmt->close();
}

// Redirect back to archived products page
header("Location: archived-products.php");
exit();
?>