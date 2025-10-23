<?php
session_start();
include("../config/db.php");

// Only staff/user can sell
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: inventory.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $customer_name = trim($_POST['customer_name']);
    $customer_contact = trim($_POST['customer_contact']);
    $customer_address = trim($_POST['customer_address']);
    $quantity = (int)$_POST['quantity'];

    // Validate
    if ($product_id <= 0 || empty($customer_name) || empty($customer_contact) || empty($customer_address) || $quantity <= 0) {
        $_SESSION['error'] = "Please fill all required fields correctly.";
        header("Location: inventory.php");
        exit();
    }

    // Get product info
    $productQuery = $conn->prepare("SELECT name, price, quantity FROM products WHERE id = ?");
    $productQuery->bind_param("i", $product_id);
    $productQuery->execute();
    $productResult = $productQuery->get_result();

    if ($productResult->num_rows === 0) {
        $_SESSION['error'] = "Product not found.";
        header("Location: inventory.php");
        exit();
    }

    $product = $productResult->fetch_assoc();

    if ($quantity > $product['quantity']) {
        $_SESSION['error'] = "Not enough stock. Current: ".$product['quantity'];
        header("Location: inventory.php");
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Insert into transactions
        $insert = $conn->prepare("
            INSERT INTO transactions
            (product_id, customer_name, customer_contact, customer_address, quantity, price_per_unit, status)
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");
        $insert->bind_param("isssid", $product_id, $customer_name, $customer_contact, $customer_address, $quantity, $product['price']);
        $insert->execute();

        // Update product stock
        $newQuantity = $product['quantity'] - $quantity;
        $update = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $update->bind_param("ii", $newQuantity, $product_id);
        $update->execute();

        $conn->commit();
        $_SESSION['success'] = "Sale recorded successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: ".$e->getMessage();
    }

    header("Location: inventory.php");
    exit();
} else {
    header("Location: inventory.php");
    exit();
}
?>