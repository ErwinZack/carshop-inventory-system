<?php
session_start();
include("../config/db.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch audit logs (newest first)
$result = $conn->query("
    SELECT audit_log.*, users.username 
    FROM audit_log
    JOIN users ON audit_log.user_id = users.id
    ORDER BY audit_log.created_at DESC
");
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div style="margin-left:220px; padding:20px;">
    <h2>Audit Log</h2>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse;">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>Date</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['action']); ?></td>
            <td><?php echo htmlspecialchars($row['details']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="5">No audit logs found.</td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>