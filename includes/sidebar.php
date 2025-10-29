<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role_id']; // 1 = Admin, 2 = User
$roleClass = ($role == 1) ? "sidebar-admin" : "sidebar-user";
?>
<!-- Font Awesome 6 Free CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<aside class="sidebar <?php echo $roleClass; ?>">
    <h3 class="sidebar-title">Menu</h3>
    <ul class="sidebar-menu">
        <?php if ($role == 1) { ?>
        <!-- Admin Sidebar Links -->
        <li>
            <a href="../admin/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="../admin/low-stock.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'low-stock.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts
            </a>
        </li>


        <li>
            <a href="../admin/manage-inventory.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-inventory.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-box"></i> Manage Inventory
            </a>
        </li>


        <!-- Archived Products -->
        <li>
            <a href="../admin/archived-products.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'archived-products.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-archive"></i> Archived Products
            </a>
        </li>


        <li>
            <a href="../admin/manage-users.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i> Manage Users
            </a>
        </li>

        <!-- <li>
            <a href="../admin/reports.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-simple"></i> Reports
            </a>
        </li> -->

        <!-- Optional future links -->
        <!-- 
<li>
  <a href="../admin/audit-log.php" 
     class="<?php echo basename($_SERVER['PHP_SELF']) == 'audit-log.php' ? 'active' : ''; ?>">
     <i class="fa-solid fa-file"></i> Audit Log
  </a>
</li>

<li>
  <a href="../admin/notifications.php" 
     class="<?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
     <i class="fa-solid fa-bell"></i> Notifications
  </a>
</li>
-->

        <?php } else { ?>
        <!-- User Sidebar Links -->
        <li>
            <a href="../user/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="../user/inventory.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-box-open"></i> Browse Inventory
            </a>
        </li>

        <!-- New Feature: Low Stock / Alerts -->
        <li>
            <a href="../user/low-stock.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'low-stock.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts
            </a>
        </li>

        <!-- New Feature: Add Sales -->
        <li>
            <a href="../user/add-sales.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'add-sales.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Add Sales
            </a>
        </li>

        <!-- Sales History / Invoice -->
        <li>
            <a href="../user/sales-history.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'sales-history.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-receipt"></i> Sales History
            </a>
        </li>

        <!-- New Feature: Reports / Analytics -->
        <li>
            <a href="../user/reports.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-simple"></i> Reports
            </a>
        </li>

        <!-- Archived Products -->
        <!-- <li>
            <a href="../user/archived-products.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'archived-products.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-archive"></i> Archived Products
            </a>
        </li> -->


        <?php } ?>
    </ul>
    <?php if ($role == 2) { ?>
    <!-- Categories Section (For Users Only) -->
    <h3 class="sidebar-title sidebar-title-dropdown">
        Categories
        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
    </h3>

    <ul class="sidebar-menu categories">
        <?php
    include("../config/db.php");

    // Get the selected category from URL (if any)
    $currentCat = isset($_GET['cat']) ? $_GET['cat'] : '';

    $categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
    if ($categories->num_rows > 0) {
        while ($cat = $categories->fetch_assoc()) {
            $category = htmlspecialchars($cat['category']);
            $isActive = ($currentCat == $category) ? 'active' : '';

            echo '
            <li>
                <a href="../user/category.php?cat=' . urlencode($category) . '" class="' . $isActive . '">
                    <i class="fa-solid fa-tag"></i> ' . $category . '
                </a>
            </li>';
        }
    } else {
        echo '<li><span>No Categories Available</span></li>';
    }
    ?>
    </ul>
    <?php } ?>

</aside>

<script>
// Dropdown Toggle Script
document.addEventListener('DOMContentLoaded', function() {
    const categoryTitle = document.querySelector('.sidebar-title-dropdown');
    const categoryMenu = document.querySelector('.sidebar-menu.categories');
    const dropdownIcon = document.querySelector('.dropdown-icon');

    if (categoryTitle && categoryMenu) {
        categoryTitle.style.cursor = 'pointer';

        categoryTitle.addEventListener('click', function() {
            categoryMenu.classList.toggle('collapsed');
            dropdownIcon.classList.toggle('rotate');
        });
    }
});
</script>