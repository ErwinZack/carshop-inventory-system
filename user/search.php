<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $result = $conn->query("SELECT * FROM products WHERE name LIKE '%$keyword%' OR category LIKE '%$keyword%'");
} else {
    $result = $conn->query("SELECT * FROM products");
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content search-page">
    <h2 class="page-title">Search Products</h2>

    <!-- Search form -->
    <form method="GET" class="search-form">
        <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>"
            placeholder="Search by name or category" class="search-input">
        <button type="submit" class="btn btn-search">Search</button>
    </form>

    <!-- Product results table -->
    <table class="product-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><a href="product-details.php?id=<?php echo $row['id']; ?>" class="btn-link">View</a></td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="no-results">No products found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>