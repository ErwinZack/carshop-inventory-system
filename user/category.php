<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../auth/login.php");
    exit();
}

// Get category from query string
if (!isset($_GET['cat'])) {
    header("Location: dashboard.php");
    exit();
}

$category = $conn->real_escape_string($_GET['cat']);

// Fetch products by category
$sql = "SELECT * FROM products WHERE category = '$category' ORDER BY name ASC";
$result = $conn->query($sql);

// === SEARCH HANDLING ===
$search = '';
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search = $conn->real_escape_string($_GET['q']);
    $sql = "SELECT * FROM products 
            WHERE category = '$category' 
              AND (name LIKE '%$search%' OR category LIKE '%$search%')
            ORDER BY name ASC";
} else {
    $sql = "SELECT * FROM products WHERE category = '$category' ORDER BY name ASC";
}

$result = $conn->query($sql);

?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content category-page">
    <h2 class="page-title"><?php echo htmlspecialchars($category); ?> Products</h2>

    <div class="products-grid">
        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="dashboard-product-card">
            <div class="dashboard-product-image-wrapper">
                <?php if (!empty($row['image'])): ?>
                <img src="../<?php echo htmlspecialchars($row['image']); ?>"
                    alt="<?php echo htmlspecialchars($row['name']); ?>" class="dashboard-product-image">
                <?php else: ?>
                <img src="../assets/no-image.png" alt="No Image" class="dashboard-product-image">
                <?php endif; ?>
            </div>

            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
            <p>Quantity: <?php echo $row['quantity']; ?></p>
            <p>Price: ₱<?php echo number_format($row['price'], 0); ?></p>

            <!-- ✅ Match dashboard's View Details button style -->
            <div class="product-card-footer">
                <a href="product-details.php?id=<?php echo $row['id']; ?>" class="view-details-btn">
                    <i class="fa-solid fa-eye"></i> View Details
                </a>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>No products found in this category.</p>
        <?php endif; ?>
    </div>

    <div class="back-link">
        <!-- <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a> -->
    </div>
</div>

<?php include("../includes/footer.php"); ?>

<script>
window.addEventListener('pageshow', function(event) {
    const searchInput = document.querySelector('input[name="q"]');
    const searchResults = document.getElementById('searchResults'); // Your dropdown container

    // Only clear input and suggestions when navigating back via bfcache
    if (event.persisted) {
        if (searchInput) searchInput.value = ''; // Clear previous search input
        if (searchResults) {
            searchResults.innerHTML = ''; // Clear old suggestions
            searchResults.style.display = 'none'; // Hide the dropdown
        }
    }

    // Optional: preserve the category in the URL without reloading
    const url = new URL(window.location.href);
    const cat = url.searchParams.get('cat');
    if (cat && !window.location.search.includes('cat=')) {
        const basePath = window.location.pathname;
        const newUrl = `${basePath}?cat=${encodeURIComponent(cat)}`;
        history.replaceState(null, '', newUrl); // update URL without reload
    }
});
</script>

<!-- 
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.value = ''; // Clear input
        }

        // Reload current page with existing query parameters preserved
        const url = new URL(window.location.href);
        const cat = url.searchParams.get('cat'); // preserve category
        const basePath = window.location.pathname;

        if (cat) {
            window.location.href = `${basePath}?cat=${encodeURIComponent(cat)}`;
        } else {
            window.location.href = basePath;
        }
    }
});
</script> -->