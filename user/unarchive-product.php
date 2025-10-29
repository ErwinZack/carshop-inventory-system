<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    $stmt = $conn->prepare("UPDATE products SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

header("Location: archive-product.php");
exit();
?>