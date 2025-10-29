<?php
session_start();
include("../config/db.php");

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 1) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user by email (no md5)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password using password_verify()
        if (password_verify($password, $row['password'])) {
            // Check if admin role
            if ($row['role_id'] == 1) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['first_name'] = !empty($row['first_name']) ? $row['first_name'] : 'Admin';
                $_SESSION['username'] = $row['username'];

                $success = "Login successful! Redirecting...";
            } else {
                $error = "Invalid email or not an admin account.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin-login-register.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
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
                    <h1>Welcome, Admin!</h1>
                    <p>Manage users, monitor service requests, view reports, and maintain system settings all from your
                        secure dashboard to keep operations running smoothly.</p>
                </div>
            </div>
        </div>

        <!-- Right side -->
        <div class="login-right">
            <div class="login-box">
                <img src="../assets/images/admin-icon.jpg" alt="Admin Logo" class="login-logo">
                <h2>Admin Login</h2>

                <form method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    <div class="password-container">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <i class="ri-eye-line toggle-password" id="togglePassword"></i>
                    </div>
                    <button type="submit">Login</button>
                    <p><a href="forgot-password.php" class="forgot-link">Forgot Password?</a></p>

                </form>

                <!-- Modern Messages -->
                <?php if($error): ?>
                <p class="message error pop"><?php echo $error; ?></p>
                <?php elseif($success): ?>
                <p class="message success pop"><?php echo $success; ?></p>
                <?php endif; ?>

                <p>No account? <a href="register.php">Register here</a></p>
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

    // Show/hide messages
    window.addEventListener('DOMContentLoaded', () => {
        const message = document.querySelector('.message');
        if (message) {
            setTimeout(() => message.classList.add('show'), 100);

            if (message.classList.contains('error')) {
                setTimeout(() => message.classList.remove('show'), 2100);
            }

            if (message.classList.contains('success')) {
                setTimeout(() => window.location.href = 'dashboard.php', 1500);
            }
        }
    });
    </script>
</body>

</html>