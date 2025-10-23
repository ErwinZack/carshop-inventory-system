<?php
session_start();
if (isset($_SESSION['role_id'])) {
    if ($_SESSION['role_id'] == 1) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
} else {
    header("Location: auth/login.php");
}
exit();
?>