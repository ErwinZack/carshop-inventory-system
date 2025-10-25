<?php
session_start();
include("../config/db.php");

// 🔹 If already logged in as admin, redirect to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 1) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = md5($_POST['password']); // same hashing method used in DB

    // 🔹 Validate admin credentials
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role_id=1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role_id'] = $row['role_id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['first_name'] = !empty($row['first_name']) ? $row['first_name'] : 'Admin';
        $_SESSION['username'] = $row['username'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or not an admin account.";
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

    <!-- ✅ Remix Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* Extra CSS for password toggle */
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            user-select: none;
        }
    </style>
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
                    <p>Manage users, view reports, and maintain system settings with your secure login.</p>
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

                    <p class="error-message"><?php echo $error; ?></p>
                    <button type="submit">Login</button>
                </form>

                <p>No account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>

    <!-- ✅ JS to toggle password visibility -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            togglePassword.className = isPassword ? 'ri-eye-off-line toggle-password' : 'ri-eye-line toggle-password';
        });
    </script>
</body>

</html>
