<?php
session_start();
include("../config/db.php");

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current admin info
$result = $conn->query("SELECT username, email, profile_image FROM users WHERE id = $user_id");
$admin = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // Handle profile image upload
    $profile_image = $admin['profile_image'];
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
            // Refresh admin data
            $result = $conn->query("SELECT username, email, profile_image FROM users WHERE id = $user_id");
            $admin = $result->fetch_assoc();
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
    <h2 class="page-title">Edit Admin Profile</h2>

    <?php if (!empty($error)): ?>
    <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
    <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>"
                required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($admin['email']); ?>"
                required>
        </div>

        <div class="form-group">
            <label>Current Profile Image:</label><br>
            <?php if (!empty($admin['profile_image'])): ?>
            <img src="../<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile Image"
                class="profile-preview">
            <?php else: ?>
            <p>No profile image uploaded.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="profile_image">Upload New Image:</label>
            <input type="file" name="profile_image" id="profile_image">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-save">Save Changes</button>
            <a href="profile.php" class="btn btn-back">← Back to Profile</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>