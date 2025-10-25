<?php
function getSearchAction($role_id) {
    $current_page = basename($_SERVER['PHP_SELF']); 

    if ($current_page === 'inventory.php') {
        return 'inventory.php';
    } elseif ($current_page === 'low-stock.php') {
        return 'low-stock.php';
    } elseif ($current_page === 'reports.php') {
        return 'reports.php';
    } elseif ($current_page === 'category.php') {
        return 'category.php';
    } elseif ($current_page === 'manage-inventory.php') {
        return 'manage-inventory.php';
    } elseif ($current_page === 'manage-users.php') {
        return 'manage-users.php';
    } else {
        return ($role_id == 1) ? '../admin/dashboard.php' : '../user/dashboard.php';
    }
}
?>
