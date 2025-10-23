<?php
include('../config/db.php');

$term = $_GET['term'] ?? '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$term = $conn->real_escape_string($term);
$sql = "SELECT id, name FROM products 
        WHERE name LIKE '%$term%' 
           OR category LIKE '%$term%' 
        ORDER BY name ASC 
        LIMIT 10";
$result = $conn->query($sql);

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($suggestions);