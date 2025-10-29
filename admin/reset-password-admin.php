<?php
session_start();
include("../config/db.php");

// Redirect if OTP not verified
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: forgot-password-admin.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    // Update only admin accounts
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND role_id = 1");
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        $message = "Password reset successful! <a href='login.php'>Login now</a>.";
        session_destroy();
    } else {
        $message = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="reset-wrapper">
        <div class="reset-container">
            <div class="reset-header">
                <h1 class="reset-title">Reset Password</h1>
                <p class="reset-subtitle">Enter your new password to update your admin account.</p>
            </div>
            <form method="POST" class="reset-form">
                <div class="reset-input-group">
                    <input type="password" name="password" class="reset-input" placeholder="New Password" required>
                </div>
                <p class="reset-error"><?php echo $message; ?></p>
                <button type="submit" class="reset-btn">Reset Password</button>
            </form>
            <div class="reset-footer">
                <a href="login.php" class="reset-back">Back to Login</a>
            </div>
        </div>
    </div>
</body>

</html>