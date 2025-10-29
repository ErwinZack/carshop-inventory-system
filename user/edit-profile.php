<!-- <?php
session_start();
include("../config/db.php");

// Redirect if not logged in or not a staff user
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current user info
$result = $conn->query("SELECT username, email, created_at, profile_image FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// Fallback profile image
$profileImage = "../assets/images/default-avatar.png";
if (!empty($user['profile_image'])) {
    $profileImage = "../" . $user['profile_image'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // Handle profile image upload
    $profile_image = $user['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = __DIR__ . "/../uploads/profiles/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $profile_image = "uploads/profiles/" . $fileName;
            } else {
                $error = "Error uploading profile image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF allowed.";
        }
    }

    if (empty($error)) {
        $update = $conn->query("UPDATE users SET username='$username', email='$email', profile_image='$profile_image' WHERE id=$user_id");
        if ($update) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $result = $conn->query("SELECT username, email, created_at, profile_image FROM users WHERE id = $user_id");
            $user = $result->fetch_assoc();
            $profileImage = !empty($user['profile_image']) ? "../" . $user['profile_image'] : "../assets/images/default-avatar.png";
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content edit-profile-page">
    <h2 class="page-title">Edit Profile</h2>

    <?php if (!empty($error)): ?>
    <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
    <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>

    <div class="profile-card">
        <!-- Profile Image -->
<div class="profile-avatar1">
    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" class="profile-img">
</div>

<!-- Profile Info Form -->
<form method="POST" enctype="multipart/form-data" class="profile-info-form">
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>"
            required>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>

    <div class="form-group">
        <label for="profile_image">Upload New Image:</label>
        <input type="file" name="profile_image" id="profile_image">
    </div>

    <div class="form-group">
        <p><strong>Role:</strong> Staff</p>
        <p><strong>Member Since:</strong> <?php echo date("F d, Y", strtotime($user['created_at'])); ?></p>
    </div>

    <div class="profile-actions">
        <button type="submit" class="btn btn-save">Save Changes</button>
        <a href="profile.php" class="btn btn-back">← Back to Profile</a>
    </div>
</form>
</div>
</div>

<?php include("../includes/footer.php"); ?> -->