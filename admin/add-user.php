<?php
session_start();
include("../config/db.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$message = "";

/* ============================================================
   HANDLE FORM SUBMISSION
   ============================================================ */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username      = trim($_POST['username']);
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $email         = trim($_POST['email']);
    $phone_number  = trim($_POST['phone_number']);
    $address       = trim($_POST['address']);
    $password      = trim($_POST['password']);
    $role_id       = $_POST['role_id'];
    $member_since  = $_POST['member_since'];
    $status        = $_POST['status'];
    $created_by    = $_SESSION['user_id'];

    if (!empty($username) && !empty($email) && !empty($password) && !empty($role_id)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='error-msg'>Please enter a valid email address.</div>";
        } else {
            // Check if username or email already exists
            $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $check->bind_param("ss", $email, $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "<div class='error-msg'>Username or email already exists.</div>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    INSERT INTO users 
                    (username, first_name, last_name, email, phone_number, address, password, role_id, status, member_since, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param(
                    "ssssssssssi",
                    $username,
                    $first_name,
                    $last_name,
                    $email,
                    $phone_number,
                    $address,
                    $hashed_password,
                    $role_id,
                    $status,
                    $member_since,
                    $created_by
                );

                if ($stmt->execute()) {
                    $message = "<div class='success-msg'>✅ User successfully added!</div>";
                } else {
                    $message = "<div class='error-msg'>❌ Error adding user. Please try again.</div>";
                }

                $stmt->close();
            }
            $check->close();
        }
    } else {
        $message = "<div class='error-msg'>All fields are required.</div>";
    }
}

/* ============================================================
   FETCH ROLES (ADMIN & USER) — display USER as STAFF
   ============================================================ */
$roles = $conn->query("
    SELECT * FROM roles 
    WHERE LOWER(role_name) IN ('admin', 'user')
    ORDER BY id ASC
");
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content add-user-page">
    <h2 class="page-title">Add New User</h2>

    <div class="add-user-container">
        <?php if (!empty($message)) echo $message; ?>

        <form method="POST" action="" class="add-user-form">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" placeholder="e.g., sales_maria"
                    required>
            </div>


            <div class="form-group">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-input" placeholder="Enter first name"
                    required>
            </div>

            <div class="form-group">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Enter last name"
                    required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter email address"
                    required>
            </div>

            <div class="form-group">
                <label for="phone_number" class="form-label">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" class="form-input"
                    placeholder="e.g., 09XXXXXXXXX">
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address:</label>
                <textarea id="address" name="address" class="form-input" rows="3"
                    placeholder="Enter address..."></textarea>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter password"
                    required>
            </div>

            <div class="form-group">
                <label for="role_id" class="form-label">Role:</label>
                <select id="role_id" name="role_id" class="form-select" required>
                    <option value="">-- Select Role --</option>
                    <?php while ($role = $roles->fetch_assoc()): ?>
                    <option value="<?php echo $role['id']; ?>">
                        <?php echo ($role['role_name'] === 'User') ? 'Staff' : htmlspecialchars($role['role_name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status:</label>
                <select id="status" name="status" class="form-select">
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="member_since" class="form-label">Member Since:</label>
                <input type="date" id="member_since" name="member_since" class="form-input"
                    value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-save"><i class="fa fa-save"></i> Save</button>
                <a href="manage-users.php" class="btn btn-cancel"><i class="fa fa-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

<script>
// Fix: make sure date picker scrolls properly into view
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('focus', () => {
        setTimeout(() => {
            input.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 100);
    });
});
</script>