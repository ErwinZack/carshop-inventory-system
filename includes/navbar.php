<?php
// Start session only if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch user info from session
$username = $_SESSION['first_name'] ?? 'Guest';
$role_id = $_SESSION['role_id'] ?? 0;
$roleClass = ($role_id == 1) ? "navbar-admin" : "navbar-user";
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/navbar.css">

<nav class="navbar <?php echo $roleClass; ?>">
    <div class="navbar-container">
        <!-- Brand with Logo -->
        <div class="navbar-brand">
            <img src="../assets/images/car-service-logo-design.png" alt="Logo" class="navbar-logo">
            <span>Car Shop Inventory</span>
        </div>

        <div class="navbar-right">
            <!-- Search -->
            <!--<div class="navbar-search" style="position: relative;">
                <form action="<?php 
                    $current_page = basename($_SERVER['PHP_SELF']); 
                    if ($current_page === 'inventory.php') {
                        echo 'inventory.php';
                    } elseif ($current_page === 'low-stock.php') {
                        echo 'low-stock.php';
                    } elseif ($current_page === 'reports.php') {
                        echo 'reports.php';
                    } elseif ($current_page === 'category.php') {
                        echo 'category.php';
                    } elseif ($current_page === 'manage-inventory.php') {
                        echo 'manage-inventory.php';
                    } elseif ($current_page === 'manage-users.php') {
                        echo 'manage-users.php';
                    } else {
                        echo ($role_id == 1) ? '../admin/dashboard.php' : '../user/dashboard.php';
                    }
                ?>" method="GET" autocomplete="off">

                    <?php if ($current_page === 'category.php' && isset($_GET['cat'])): ?>
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($_GET['cat']); ?>">
                    <?php endif; ?>

                    <input type="text" id="searchInput" name="q" placeholder="Search products..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                        style="padding-right: 35px;">

                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        viewBox="0 0 24 24"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <path
                            d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
                    </svg>
                </form>

                <div id="searchResults" class="search-results"></div>
            </div>-->

            <!-- Notifications -->
            <div class="navbar-notifications">
                <a href="javascript:void(0);" id="notificationsBtn" class="notification-link" title="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 24c1.104 0 2-.896 2-2h-4c0 1.104.896 2 2 2zm6.364-6v-5c0-3.07-1.641-5.64-4.364-6.32V6c0-.828-.671-1.5-1.5-1.5S11 5.172 11 6v.68C8.277 7.36 6.636 9.93 6.636 13v5l-2 2v1h16v-1l-2-2z" />
                    </svg>
                    <span class="notification-badge"></span>
                </a>

                <div id="notificationsDropdown">
                    <div class="notifications-header">Notifications</div>
                    <div id="notificationsContent">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Profile & Welcome -->
            <div class="navbar-icons">
                <div class="navbar-profile-welcome">
                    <!-- Profile Icon -->
                    <button class="profile-icon-btn" id="profileIconBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3 0-9 1.5-9 4.4V22h18v-3.4c0-2.9-6-4.4-9-4.4z" />
                        </svg>
                    </button>

                    <!-- Welcome Text + Dropdown -->
                    <button class="welcome-btn" id="welcomeBtn">
                        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($username); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 16 16" class="dropdown-arrow">
                            <path fill-rule="evenodd"
                                d="M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown-menu" id="profileMenu">
                        <a href="<?php echo $role_id == 1 ? '../admin/profile.php' : '../user/profile.php'; ?>"
                            class="profile-dropdown-item">My Account</a>
                        <a href="<?php echo $role_id == 1 ? '../admin/logout.php' : '../user/logout.php'; ?>"
                            class="profile-dropdown-item logout-link">Logout</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</nav>

<script src="../assets/js/search.js"></script>

<script>
// Notifications toggle
const notificationsBtn = document.getElementById('notificationsBtn');
const notificationsDropdown = document.getElementById('notificationsDropdown');
const notificationsContent = document.getElementById('notificationsContent');

if (notificationsBtn && notificationsDropdown) {
    notificationsBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationsDropdown.style.display = notificationsDropdown.style.display === 'block' ? 'none' :
            'block';
        if (notificationsDropdown.style.display === 'block') {
            fetch('../includes/fetch_notifications.php')
                .then(res => res.text())
                .then(html => notificationsContent.innerHTML = html)
                .catch(err => {
                    notificationsContent.innerHTML = '<p>Error loading notifications.</p>';
                    console.error(err);
                });
        }
    });
}

// Toggle profile dropdown
const welcomeBtn = document.getElementById('welcomeBtn');
const profileMenu = document.getElementById('profileMenu');

if (welcomeBtn && profileMenu) {
    welcomeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!profileMenu.contains(e.target) && e.target !== welcomeBtn) {
            profileMenu.style.display = 'none';
        }
    });
}
</script>