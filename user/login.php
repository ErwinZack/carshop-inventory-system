<?php
session_start();
include("../config/db.php");

// Redirect if already logged in as staff
if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, first_name, email, password, role_id FROM users WHERE email = ? AND role_id = 2 LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['username'] = $row['username'];

            $success = "Login successful! Redirecting...";
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or not a staff account.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="../assets/css/user-login-register.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-left">
            <div class="decorative-shapes">
                <span class="shape shape1"></span>
                <span class="shape shape2"></span>
                <span class="shape shape3"></span>
            </div>
            <div class="left-content">
                <img src="../assets/images/car-service-logo-design.png" alt="AutoCare Logo" class="autocare-logo">
                <div class="welcome-text">
                    <h1>Welcome to AutoCare!</h1>
                    <p>Stay organized, manage customer requests, and track service schedules effortlessly to keep your
                        workshop running smoothly.</p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-box">
                <img src="../assets/images/staff-icon.jpg" alt="Staff Logo" class="login-logo">
                <h2>Login</h2>

                <form method="POST">
                    <input type="email" name="email" placeholder="Email" required autocomplete="email">
                    <div class="password-container">
                        <input type="password" name="password" id="password" placeholder="Password" required
                            autocomplete="current-password">
                        <i class="ri-eye-line toggle-password" id="togglePassword"></i>
                    </div>
                    <button type="submit">Login</button>
                    <p><a href="forgot-password.php" class="forgot-link">Forgot Password?</a></p>
                </form>

                <!-- Messages -->
                <?php if($error): ?>
                <p class="message error"><?php echo $error; ?></p>
                <?php elseif($success): ?>
                <p class="message success"><?php echo $success; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
        const isPassword = passwordField.type === 'password';
        passwordField.type = isPassword ? 'text' : 'password';
        togglePassword.className = isPassword ? 'ri-eye-off-line toggle-password' :
            'ri-eye-line toggle-password';
    });

    // Handle messages
    window.addEventListener('DOMContentLoaded', () => {
        const message = document.querySelector('.message');
        if (message) {
            // Show message with CSS transition
            message.classList.add('show');

            // If success message, redirect after 1.5s
            if (message.classList.contains('success')) {
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1500);
            }

            // Hide error message automatically after 2s
            if (message.classList.contains('error')) {
                setTimeout(() => {
                    message.classList.remove('show');
                }, 2000);
            }

            // Hide message if user starts typing
            const emailField = document.querySelector('input[name="email"]');
            const passwordFieldInput = document.querySelector('input[name="password"]');
            [emailField, passwordFieldInput].forEach(field => {
                field.addEventListener('input', () => message.classList.remove('show'));
            });
        }
    });
    </script>
</body>

</html>