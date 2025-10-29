<?php
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset-password-admin.php"); // Admin password reset page
        exit();
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="verify-wrapper">
        <div class="verify-container">
            <div class="verify-header">
                <h1 class="verify-title">Verify OTP</h1>
                <p class="verify-subtitle">Enter the OTP sent to your admin email to reset your password.</p>
            </div>
            <form method="POST" class="verify-form">
                <div class="verify-input-group">
                    <input type="text" name="otp" class="verify-input" placeholder="Enter OTP" required>
                </div>
                <p class="verify-error"><?php echo $message; ?></p>
                <button type="submit" class="verify-btn">Verify OTP</button>
            </form>
            <div class="verify-footer">
                <a href="forgot-password-admin.php" class="verify-back">Back to Forgot Password</a>
            </div>
        </div>
    </div>
</body>

</html>