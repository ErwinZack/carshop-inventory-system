<?php
session_start();
include("../config/db.php");

// Redirect if already logged in as staff
if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, first_name, email, password, role_id FROM users WHERE email = ? AND role_id = 2 LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['email'] = $row['email'];

            // Set first_name and username in session for navbar display
            $_SESSION['first_name'] = $row['first_name']; // can be NULL
            $_SESSION['username'] = $row['username'];     // fallback if first_name is NULL

            header("Location: dashboard.php");
            exit();
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
                    <h1>Welcome!</h1>
                    <p>Access your tools, manage customer requests, and stay updated on service schedules.</p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-box">
                <img src="../assets/images/staff-icon.jpg" alt="Staff Logo" class="login-logo">
                <h2>Login</h2>

                <form method="POST">
                    <input type="email" name="email" placeholder="Email" required autocomplete="email">
                    <input type="password" name="password" placeholder="Password" required
                        autocomplete="current-password">
                    <p class="error-message"><?php echo $error; ?></p>
                    <button type="submit">Login</button>
                    <p><a href="forgot-password.php">Forgot Password?</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>