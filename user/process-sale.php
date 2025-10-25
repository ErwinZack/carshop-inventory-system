<?php
session_start();
include("../config/db.php"); // ✅ This initializes $conn

// Only role_id 2 (staff/user) can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

// CSRF validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token.');
}

// Get posted form data
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_contact = trim($_POST['customer_contact'] ?? '');
$customer_address = trim($_POST['customer_address'] ?? '');
$items = $_POST['items'] ?? []; // array: each item -> ['product_id', 'qty', 'price']

$errors = [];

// Basic validation
if (empty($customer_name)) $errors[] = 'Customer name is required.';
if (empty($customer_contact)) $errors[] = 'Customer contact is required.';
if (empty($customer_address)) $errors[] = 'Customer address is required.';
if (!is_array($items) || count($items) === 0) $errors[] = 'No items to process.';

if (!empty($errors)) {
    foreach ($errors as $err) {
        echo '<p>' . htmlspecialchars($err) . '</p>';
    }
    echo '<p><a href="checkout.php">Back</a></p>';
    exit;
}

// Clean and prepare items
$preparedItems = [];
foreach ($items as $it) {
    $pid = (int)($it['product_id'] ?? 0);
    $qty = (int)($it['qty'] ?? 0);
    $price = (float)($it['price'] ?? 0);
    if ($pid > 0 && $qty > 0 && $price > 0) {
        $preparedItems[$pid] = [
            'product_id' => $pid,
            'qty' => $qty,
            'price' => $price
        ];
    }
}

if (count($preparedItems) === 0) {
    die('No valid items provided.');
}

$conn->begin_transaction();

try {
    // Stock validation
    $product_ids = array_keys($preparedItems);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $sql = "SELECT id, name, quantity FROM products WHERE id IN ($placeholders) FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($product_ids));
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();
    $res = $stmt->get_result();

    $dbProducts = [];
    while ($row = $res->fetch_assoc()) {
        $dbProducts[(int)$row['id']] = $row;
    }

    foreach ($preparedItems as $pid => $it) {
        if (!isset($dbProducts[$pid])) {
            throw new Exception("Product ID $pid not found.");
        }
        if ($it['qty'] > (int)$dbProducts[$pid]['quantity']) {
            throw new Exception("Insufficient stock for " . htmlspecialchars($dbProducts[$pid]['name']));
        }
    }

    // Prepare transaction & stock update queries
    $insertStmt = $conn->prepare("INSERT INTO transactions 
        (product_id, customer_name, customer_contact, customer_address, quantity, price_per_unit, sale_date, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 'active', NOW())");

    $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");

    foreach ($preparedItems as $it) {
        $insertStmt->bind_param(
            "isssid",
            $it['product_id'],
            $customer_name,
            $customer_contact,
            $customer_address,
            $it['qty'],
            $it['price']
        );
        if (!$insertStmt->execute()) {
            throw new Exception("Error inserting transaction.");
        }

        $updateStmt->bind_param("ii", $it['qty'], $it['product_id']);
        if (!$updateStmt->execute()) {
            throw new Exception("Error updating product stock.");
        }
    }

    $conn->commit();
    header('Location: sales-history.php?status=ok');
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("Sale error: " . $e->getMessage());
    echo "<h3>Sale Failed</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo '<p><a href="checkout.php">Back to Checkout</a></p>';
    exit;
}
