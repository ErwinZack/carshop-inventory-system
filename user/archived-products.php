<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Filter archived products by search query if provided
// $search = '';
// if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
//     $search = $conn->real_escape_string(trim($_GET['q']));
//     $query = "
//         SELECT * FROM products
//         WHERE status = 'archived'
//         AND (name LIKE '%$search%' OR category LIKE '%$search%')
//         ORDER BY category ASC, name ASC
//     ";
// } else {
//     $query = "SELECT * FROM products WHERE status = 'archived' ORDER BY category ASC, name ASC";
// }

// $result = $conn->query($query);



// Fetch archived products
$query = "SELECT * FROM products WHERE status = 'archived' ORDER BY category ASC, name ASC";
$result = $conn->query($query);
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-archive"></i> Archived Products</h2>


    <?php if ($result->num_rows > 0): ?>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
                <td>
                    <a href="unarchive-product.php?id=<?php echo $row['id']; ?>" class="btnr btn-restore"
                        onclick="return confirm('Are you sure you want to restore this product?');">
                        <i class="fa-solid fa-rotate-left"></i> Restore
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align:center; margin-top:20px;">No archived products found.</p>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>