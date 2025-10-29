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
                        <a href="#" class="profile-dropdown-item logout-link">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#f44336" viewBox="0 0 24 24">
                <path
                    d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
            </svg>
            <h2>Confirm Logout</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to logout?</p>
        </div>
        <div class="modal-footer">
            <button id="cancelLogout" class="btn btn-cancel">Cancel</button>
            <a id="confirmLogout" href="#" class="btn btn-logout">Logout</a>
        </div>
    </div>
</div>

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

// Logout Modal
const logoutLinks = document.querySelectorAll('.logout-link');
const logoutModal = document.getElementById('logoutModal');
const cancelLogout = document.getElementById('cancelLogout');
const confirmLogout = document.getElementById('confirmLogout');

logoutLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        logoutModal.style.display = 'block';
        // Set the correct logout URL depending on role
        confirmLogout.href =
            <?php echo ($role_id == 1) ? "'../admin/logout.php'" : "'../user/logout.php'"; ?>;
    });
});

cancelLogout.addEventListener('click', () => {
    logoutModal.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === logoutModal) {
        logoutModal.style.display = 'none';
    }
});
</script>