<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function check_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if (!isset($_POST['csrf_token']) || !check_csrf($_POST['csrf_token'])) {
    die('Invalid CSRF token.');
}

$selected = $_POST['selected'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (!is_array($selected) || count($selected) === 0) {
    header('Location: add-sales.php');
    exit;
}

$ids = array_map('intval', $selected);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "SELECT id, name, category, quantity AS available_qty, price FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) {
    $pid = (int)$row['id'];
    $requested = isset($qtys[$pid]) ? (int)$qtys[$pid] : 1;
    if ($requested < 1) $requested = 1;
    $row['requested_qty'] = $requested;
    $items[$pid] = $row;
}

if (count($items) === 0) {
    header('Location: add-sales.php');
    exit;
}
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
    .checkout-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .checkout-table th,
    .checkout-table td {
        padding: 10px;
        border: 1px solid #eee;
    }

    .form-row {
        margin-bottom: 12px;
    }

    .input,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
    }
    </style>
</head>

<body>

    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <h1>Checkout</h1>

            <form method="post" action="process-sale.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <h3>Items to Sell</h3>
                <table class="checkout-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Available</th>
                            <th>Quantity</th>
                            <th>Price (₱)</th>
                            <th>Subtotal (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
            $grandTotal = 0;
            foreach ($items as $id => $it):
                $subtotal = $it['requested_qty'] * (float)$it['price'];
                $grandTotal += $subtotal;
            ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['name']); ?></td>
                            <td><?php echo htmlspecialchars($it['category']); ?></td>
                            <td><?php echo (int)$it['available_qty']; ?></td>
                            <td><?php echo (int)$it['requested_qty']; ?></td>
                            <td><?php echo number_format($it['price'], 2); ?></td>
                            <td><?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <!-- Hidden inputs to pass to processing -->
                        <input type="hidden" name="items[<?php echo (int)$id; ?>][product_id]"
                            value="<?php echo (int)$id; ?>">
                        <input type="hidden" name="items[<?php echo (int)$id; ?>][qty]"
                            value="<?php echo (int)$it['requested_qty']; ?>">
                        <input type="hidden" name="items[<?php echo (int)$id; ?>][price]"
                            value="<?php echo htmlspecialchars($it['price']); ?>">
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;"><strong>Total:</strong></td>
                            <td><strong>₱ <?php echo number_format($grandTotal, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <h3>Customer Details</h3>
                <div class="form-row">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="input" maxlength="100" required>
                </div>

                <div class="form-row">
                    <label>Customer Contact</label>
                    <input type="text" name="customer_contact" class="input" maxlength="50" required>
                </div>

                <div class="form-row">
                    <label>Customer Address</label>
                    <textarea name="customer_address" class="input" rows="3" maxlength="255" required></textarea>
                </div>

                <p>
                    <button type="submit" class="checkout-btn-confirm">Confirm Sale</button>
                </p>

                <!-- Mini form to return selections -->
                <form method="post" action="add-sales.php" style="display: inline;">
                    <?php foreach ($items as $id => $it): ?>
                    <input type="hidden" name="selected[]" value="<?php echo (int)$id; ?>">
                    <input type="hidden" name="qty[<?php echo (int)$id; ?>]"
                        value="<?php echo (int)$it['requested_qty']; ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="checkout-btn-modify">Modify Selection</button>
                </form>

        </div>
    </div>
</body>

</html>