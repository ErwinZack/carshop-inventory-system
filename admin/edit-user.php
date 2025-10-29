<?php
session_start();
include("../config/db.php");

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Get user ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-users.php");
    exit();
}

$user_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch user info
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
if ($result->num_rows == 0) {
    header("Location: manage-users.php");
    exit();
}
$user = $result->fetch_assoc();

// Handle form submission
if (isset($_POST['update_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $role_id = intval($_POST['role']);

    $update = $conn->query("
        UPDATE users SET 
        username='$username', 
        email='$email',
        first_name='$first_name',
        last_name='$last_name',
        phone_number='$phone',
        address='$address',
        role_id=$role_id
        WHERE id=$user_id
    ");

    if ($update) {
        $success = "User updated successfully!";
        $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
        $user = $result->fetch_assoc();
    } else {
        $error = "Failed to update user.";
    }
}

// Fetch roles
$roles_result = $conn->query("SELECT * FROM roles");
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content profile-page" style="margin-top: 60px;">
    <h2 class="page-title">
        <i class="fa fa-edit"></i> Edit User
    </h2>

    <div class="profile-card"
        style="background: transparent; box-shadow: none; padding: 25px; margin-top: -40px; border-radius: 10px;">
        <div class="profile-edit">
            <h3>Edit User Information</h3>

            <!-- ✅ Flash Messages (kept in original position) -->
            <?php if ($error): ?>
            <p class="edit-user-flash-message error"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
            <p class="edit-user-flash-message success"><?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST" class="profile-info-form">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                </div>

                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                </div>

                <div class="form-group full-width">
                    <label>Address:</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <!-- Role -->
                <div class="form-group" style="display: flex; flex-direction: column; margin-bottom: 15px;">
                    <label style="font-weight: 600; margin-bottom: 6px;">Role:</label>
                    <select name="role" required
                        style="padding: 10px 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; background: #fff;">
                        <?php while ($role = $roles_result->fetch_assoc()): ?>
                        <option value="<?php echo $role['id']; ?>"
                            <?php if ($user['role_id'] == $role['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($role['role_name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="profile-actions" style="display: flex; gap: 10px;">
                    <button type="submit" name="update_user" class="btn btn-save"
                        style="padding: 10px 20px; box-sizing: border-box;">Save Changes</button>
                    <a href="manage-users.php" class="btn btn-cancel"
                        style="padding: 10px 20px; display: inline-block; box-sizing: border-box;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

<!-- ✅ Flash Message Styles -->
<style>
.edit-user-flash-message {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    width: 36%;
    transition: opacity 0.6s ease-in-out, max-height 0.6s ease, margin 0.6s ease, padding 0.6s ease;
    margin: 0;
    /* 👈 Prevent extra gap when hidden */
    padding: 0;
    /* 👈 Prevent spacing when hidden */
}

/* When visible */
.edit-user-flash-message.show {
    opacity: 1;
    max-height: 80px;
    margin: 5px 0 10px 0;
    /* 👈 Small, neat spacing */
    padding: 10px 15px;
    /* 👈 Compact padding */
    border-radius: 6px;
    font-size: 14px;
}

/* Success + Error Colors */
.edit-user-flash-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.edit-user-flash-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<!-- ✅ Flash Message JS Transition -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const msg = document.querySelector(".edit-user-flash-message");
    if (msg) {
        // Fade in
        setTimeout(() => msg.classList.add("show"), 100);

        // Fade out after 2.5 seconds
        setTimeout(() => {
            msg.classList.remove("show");

            // Fully remove spacing after fade-out animation
            setTimeout(() => {
                msg.style.display = "none";
            }, 600); // matches CSS transition time
        }, 2000);
    }
});
</script>