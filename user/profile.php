<?php
session_start();
include("../config/db.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Determine role
$role_id = $_SESSION['role_id'];
$roleName = ($role_id == 1) ? 'Admin' : 'Staff';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$password_error = '';
$password_success = '';

// Fetch current user info
$result = $conn->query("SELECT username, email, first_name, last_name, phone_number, address, created_at, profile_image, password FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// Fallback profile image
$profileImage = !empty($user['profile_image']) ? "../" . $user['profile_image'] : "../assets/images/default-avatar.png";

// Handle avatar change
if (isset($_POST['update_avatar']) && !empty($_FILES['profile_image']['name'])) {
    $profile_image = $user['profile_image'];
    $targetDir = __DIR__ . "/../uploads/profiles/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
            $profile_image = "uploads/profiles/" . $fileName;
            $update = $conn->query("UPDATE users SET profile_image='$profile_image' WHERE id=$user_id");
            if ($update) {
                $success = "Profile avatar updated!";
                $profileImage = "../" . $profile_image;
            } else {
                $error = "Failed to update avatar.";
            }
        } else {
            $error = "Error uploading avatar.";
        }
    } else {
        $error = "Only JPG, JPEG, PNG & GIF allowed.";
    }
}

// Handle profile info update
if (isset($_POST['update_profile'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);

    $update = $conn->query("
        UPDATE users SET 
        first_name='$first_name', 
        last_name='$last_name', 
        phone_number='$phone_number', 
        address='$address' 
        WHERE id=$user_id
    ");

    if ($update) {
        $success = "Profile updated successfully!";
        $result = $conn->query("SELECT username, email, first_name, last_name, phone_number, address, created_at, profile_image FROM users WHERE id = $user_id");
        $user = $result->fetch_assoc();
    } else {
        $error = "Failed to update profile.";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (!password_verify($current_pass, $user['password'])) {
        $password_error = "Current password is incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $password_error = "New passwords do not match.";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update_pass = $conn->query("UPDATE users SET password='$hashed_pass' WHERE id=$user_id");
        if ($update_pass) {
            $password_success = "Password changed successfully!";
        } else {
            $password_error = "Failed to change password.";
        }
    }
}
?>

<style>
.success-message,
.error-message {
    padding: 12px 18px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 500;
    width: fit-content;
    animation: fadeIn 0.3s ease-in-out;
    transition: opacity 0.5s ease-out;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>


<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content profile-page">
    <h2 class="page-title">
        <i class="fa-solid fa-user"></i> My Profile
    </h2>

    <div class="profile-card">
        <!-- Profile Avatar -->
        <div class="profile-avatar">
            <form method="POST" enctype="multipart/form-data" id="avatarForm">
                <div class="avatar-wrapper">
                    <label for="profile_image_input">
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture"
                            class="profile-img clickable-avatar" title="Click to change avatar">
                        <div class="avatar-overlay">
                            <i class="fa fa-camera"></i>
                        </div>
                    </label>
                </div>
                <input type="file" name="profile_image" id="profile_image_input" style="display:none" accept="image/*">
                <input type="hidden" name="update_avatar" value="1">
            </form>
        </div>

        <!-- Profile Info (Read-only) -->
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Full Name:</strong>
                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <!-- <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p> -->
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Role:</strong> <?php echo $roleName; ?></p>
            <p><strong>Member Since:</strong> <?php echo date("F d, Y", strtotime($user['created_at'])); ?></p>
        </div>

        <!-- Edit Profile Form -->
        <div class="profile-edit">
            <h3>Edit Personal Info</h3>
            <?php if ($error) echo "<p class='error-message'>$error</p>"; ?>
            <?php if ($success) echo "<p class='success-message'>$success</p>"; ?>

            <form method="POST" class="profile-info-form">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="text" name="phone_number"
                        value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                </div>
                <div class="form-group full-width">
                    <label>Address:</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div class="profile-actions">
                    <button type="submit" name="update_profile" class="btn btn-save">Save Changes</button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="profile-password">
            <h3>Change Password</h3>
            <?php if ($password_error) echo "<p class='error-message'>$password_error</p>"; ?>
            <?php if ($password_success) echo "<p class='success-message'>$password_success</p>"; ?>

            <form method="POST" class="profile-password-form">
                <div class="form-group">
                    <label>Current Password:</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="profile-actions">
                    <button type="submit" name="change_password" class="btn btn-save">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include("../includes/footer.php"); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const avatarInput = document.getElementById("profile_image_input");
    const avatarForm = document.getElementById("avatarForm");

    avatarInput.addEventListener("change", () => {
        if (avatarInput.files.length > 0) {
            avatarForm.submit();
        }
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const avatarInput = document.getElementById("profile_image_input");
    const avatarForm = document.getElementById("avatarForm");

    avatarInput.addEventListener("change", () => {
        if (avatarInput.files.length > 0) {
            avatarForm.submit();
        }
    });

    // Auto-hide messages after 2 seconds
    const messages = document.querySelectorAll('.success-message, .error-message');
    if (messages.length) {
        setTimeout(() => {
            messages.forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => msg.style.display = 'none', 500); // wait for fade-out
            });
        }, 2000);
    }
});
</script>