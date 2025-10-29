<?php
session_start();
include("../config/db.php");

// ✅ Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    exit('Not authorized');
}

$userRole = $_SESSION['role_id'] ?? 0; // 1 = admin, others = users
$notifications = [];

/* 🟢 ADMIN: Fetch Low-Stock Products (threshold <= 10) */
if ($userRole == 1) {
    $lowStockQuery = "
        SELECT name, quantity, created_at
        FROM products
        WHERE quantity <= 10
        ORDER BY created_at DESC
        LIMIT 5
    ";
    $lowStockResult = $conn->query($lowStockQuery);


    if ($lowStockResult && $lowStockResult->num_rows > 0) {
        while ($row = $lowStockResult->fetch_assoc()) {
             $time = strtotime($row['created_at'] ?? date('Y-m-d H:i:s'));
            $notifications[] = [
                'type' => 'low_stock',
                'time' => $time,
                'message' => "⚠️ <strong>" . htmlspecialchars($row['name']) . "</strong> – Only " . intval($row['quantity']) . " left in stock!"
            ];
        }
    }

/* 🟢 USER: Fetch New Products + Low-Stock Items */
} else {
    // 🆕 New Products (most recent first)
    $newProductQuery = "
        SELECT 
            p.name AS product_name, 
            p.created_at, 
            u.first_name, 
            u.last_name
        FROM products p
        LEFT JOIN users u ON p.added_by = u.id
        WHERE p.created_at IS NOT NULL
        ORDER BY p.created_at DESC
        LIMIT 10
    ";
    $newProductResult = $conn->query($newProductQuery);

    if ($newProductResult && $newProductResult->num_rows > 0) {
        while ($row = $newProductResult->fetch_assoc()) {
            $adminName = trim(htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')));
            if (empty($adminName)) {
                $adminName = 'the admin';
            }

            $productName = htmlspecialchars($row['product_name']);
            $createdAt = strtotime($row['created_at']);

            $notifications[] = [
                'type' => 'new_product',
                'time' => $createdAt,
                'message' => "🆕 A new item, <strong>{$productName}</strong>, was just added."
            ];
        }
    }

    // ⚠️ Low-Stock Products (for user awareness)
    $lowStockQuery = "
        SELECT name, quantity, created_at
        FROM products
        WHERE quantity < 5
        ORDER BY created_at DESC
        LIMIT 10
    ";
    $lowStockResult = $conn->query($lowStockQuery);

    if ($lowStockResult && $lowStockResult->num_rows > 0) {
        while ($row = $lowStockResult->fetch_assoc()) {
            $time = strtotime($row['created_at']);
            $notifications[] = [
                'type' => 'low_stock',
                'time' => $time,
                'message' => "⚠️ <strong>" . htmlspecialchars($row['name']) . "</strong> – Only " . intval($row['quantity']) . " left in stock!"
            ];
        }
    }
}

/* Sort Notifications by Newest First */
usort($notifications, function ($a, $b) {
    return $b['time'] <=> $a['time'];
});

/* Limit to 7 Notifications */
$notifications = array_slice($notifications, 0, 5);

/* Display Notifications */
if (empty($notifications)) {
    echo "<p>No notifications.</p>";
} else {
    echo "<style>
        .notification-item.low_stock { color: #d9534f; } /* red */
        // .notification-item.new_product { color: #5cb85c; } /* green */
    </style>";

    echo "<ul style='list-style: none; padding: 0; margin: 0;'>";

    foreach ($notifications as $note) {
        $typeClass = htmlspecialchars($note['type']);
        echo "<li class='notification-item {$typeClass}' 
                  style='padding: 6px 0; border-bottom: 1px solid #eee;'>
                  {$note['message']}
              </li>";
    }

    echo "</ul>";
}

$conn->close();
?>