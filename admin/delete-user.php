<?php
session_start();
include("../config/db.php");

// ✅ Ensure only admin can delete
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// ✅ Check if the ID is passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // 🧩 Optional: Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        echo "<script>
                alert('You cannot delete your own account.');
                window.location.href = 'manage-users.php';
              </script>";
        exit();
    }

    // ✅ Prepare delete query
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('User deleted successfully!');
                window.location.href = 'manage-users.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting user. Please try again.');
                window.location.href = 'manage-users.php';
              </script>";
    }

    $stmt->close();
} else {
    // If no valid ID is provided
    header("Location: manage-users.php");
    exit();
}

$conn->close();
?>