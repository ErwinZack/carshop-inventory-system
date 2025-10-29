<?php
session_start();
include("../config/db.php");

// Include PHPMailer files
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['reset_email'] = $email;

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'erro.zacarias.up@phinmaed.com'; // your Gmail
        $mail->Password = 'qlhsvmuwvpeembvx';              // your Gmail app password (NO SPACES)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('erro.zacarias.up@phinmaed.com', 'CarShop Auto Care Support');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'CarShop Auto Care - Password Reset OTP';
        $mail->Body = "
            <h2>Password Reset Request</h2>
            <p>Hello,</p>
            <p>Your OTP code is: <b style='font-size:20px;'>$otp</b></p>
            <p>This code will expire in 10 minutes.</p>
            <br>
            <p>If you did not request this, please ignore this email.</p>
        ";

        $mail->send();
        header("Location: verify-otp.php");
        exit();
    } catch (Exception $e) {
        $message = "Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <h1 class="forgot-title">Forgot Password?</h1>
                <p class="forgot-subtitle">Enter your email address and we'll send you an OTP to reset your password.
                </p>
            </div>
            <form method="POST" class="forgot-form">
                <div class="input-group">
                    <input type="email" name="email" class="forgot-input" placeholder="Email address" required>
                </div>
                <p class="error-message"><?php echo $message; ?></p>
                <button type="submit" class="forgot-btn">Send OTP</button>
            </form>
            <div class="forgot-footer">
                <a href="login.php" class="back-to-login">Back to Login</a>
            </div>
        </div>
    </div>

</body>

</html>