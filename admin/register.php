<?php
session_start();
include("../config/db.php");

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = md5($_POST['password']); // hash password
    $confirm = md5($_POST['confirm_password']);

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
        if ($check->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $conn->query("INSERT INTO users (username, email, password, role_id) VALUES ('$username', '$email', '$password', 1)");
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../assets/css/admin-login-register.css">
</head>

<body>
    <div class="login-wrapper">
        <!-- Left side -->
        <div class="login-left">
            <div class="decorative-shapes">
                <span class="shape shape1"></span>
                <span class="shape shape2"></span>
                <span class="shape shape3"></span>
            </div>

            <div class="left-content">
                <img src="../assets/images/car-service-logo-design.png" alt="AutoCare Logo" class="autocare-logo">
                <div class="welcome-text">
                    <h1>Join the Admin Team!</h1>
                    <p>Create your account to manage users, reports, and system operations efficiently.</p>
                </div>
            </div>
        </div>

        <!-- Right side -->
        <div class="login-right">
            <div class="login-box">
                <img src="../assets/images/admin-icon.jpg" alt="Admin Logo" class="login-logo">
                <h2>Admin Registration</h2>

                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <p class="error-message"><?php echo $error; ?></p>
                    <button type="submit">Register</button>
                </form>

                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>

</html>