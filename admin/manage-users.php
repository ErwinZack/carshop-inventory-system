<?php
session_start();
include("../config/db.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle search query from navbar
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $query = "
        SELECT users.id, users.username, users.first_name, users.last_name, users.email, users.role_id, users.created_at, roles.role_name
        FROM users
        JOIN roles ON users.role_id = roles.id
        WHERE users.username LIKE '%$search%'
        OR users.first_name LIKE '%$search%'
        OR users.last_name LIKE '%$search%'
        OR users.email LIKE '%$search%'
        OR roles.role_name LIKE '%$search%'
        ORDER BY users.id ASC
    ";
} else {
    $query = "
        SELECT users.id, users.username, users.first_name, users.last_name, users.email, users.role_id, users.created_at, roles.role_name
        FROM users
        JOIN roles ON users.role_id = roles.id
        ORDER BY users.id ASC
    ";
}

$result = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content inventory-page">
    <h2 class="page-title">
        <i class="fa-solid fa-users"></i> Manage Users
    </h2>

    <div class="inventory-header">
        <a href="add-user.php" class="btn btn-add"><i class="fa fa-plus"></i> Add User</a>
    </div>

    <table class="inventory-table">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <!-- <td><?php echo $user['id']; ?></td> -->
                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                <td><?php echo date("F d, Y", strtotime($user['created_at'])); ?></td>
                <td class="action-buttons">
                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="action-icon edit-icon" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="delete-user.php?id=<?php echo $user['id']; ?>" class="action-icon delete-icon"
                        title="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">No users found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>

<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
    }
});
</script>