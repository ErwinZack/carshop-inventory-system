<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

// Fetch all active products
$query = "SELECT * FROM products WHERE status = 'active' ORDER BY category ASC, name ASC";
$result = $conn->query($query);

// Handle search query
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $query = "
        SELECT * FROM products 
        WHERE status = 'active' AND (name LIKE '%$search%' OR category LIKE '%$search%')
        ORDER BY category ASC, name ASC
    ";
    $result = $conn->query($query);
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>
<?php include '../includes/getSearchAction.php'; ?>

<form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off">

<form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off">
    <?php if ($current_page === 'category.php' && isset($_GET['cat'])): ?>
        <input type="hidden" name="cat" value="<?php echo htmlspecialchars($_GET['cat']); ?>">
    <?php endif; ?>

    <input type="text" id="searchInput" name="q" placeholder="Search products..."
        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
        style="padding-right: 35px;">

</form>


<!-- Main Content -->
<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-boxes-stacked"></i> Inventory</h2>

    <div class="page-search">
        <form action="<?php echo getSearchAction($role_id); ?>" method="GET" autocomplete="off" style="position: relative;">
            <?php if (basename($_SERVER['PHP_SELF']) === 'category.php' && isset($_GET['cat'])): ?>
                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($_GET['cat']); ?>">
            <?php endif; ?>

            <input type="text" id="searchInput" name="q" placeholder="Search products..."
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                style="padding-right: 35px;">

            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                viewBox="0 0 24 24"
                style="position: absolute; left: 320px; right: 5px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <path
                    d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
            </svg>
        </form>

        <div id="searchResults" class="search-results"></div>
    </div>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?php echo ($row['quantity'] <= 10) ? 'low-stock-row' : ''; ?>">
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>₱<?php echo number_format($row['price'], 0); ?></td>
                <td class="action-buttons">
                    <!-- View Details -->
                    <a href="product-details.php?id=<?php echo $row['id']; ?>" class="action-icon details-link"
                        title="View Details">
                        <i class="fa fa-eye"></i>
                    </a>

                    <!-- Sell Product -->
                    <!--<button class="action-icon sell-icon"
                        onclick="openSellModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>', <?php echo $row['price']; ?>)"
                        title="Sell Product">
                        <i class="fa fa-cart-plus"></i>
                    </button>-->

                    <!-- Archive Product -->
                    <a href="archive-product.php?id=<?php echo $row['id']; ?>" class="action-icon archive-icon"
                        title="Archive Product"
                        onclick="return confirm('Are you sure you want to archive this product?');">
                        <i class="fa fa-archive"></i>
                    </a>
                </td>
            </tr>

            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Sell Modal -->
<!--<div id="sellModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSellModal()">&times;</span>
        <h3>Sell Product</h3>
        <form action="sell-product.php" method="POST">
            <input type="hidden" name="product_id" id="sellProductId">

            <p>Product: <span id="sellProductName"></span></p>
            <p>Price: ₱<span id="sellProductPrice"></span></p>

            <label>Customer Name</label>
            <input type="text" name="customer_name" required>

            <label>Customer Contact</label>
            <input type="text" name="customer_contact" required>

            <label>Customer Address</label>
            <input type="text" name="customer_address" required>

            <label>Quantity to Sell</label>
            <input type="number" name="quantity" min="1" required>

            <button type="submit" class="btn btn-sell">Confirm Sale</button>
        </form>
    </div>
</div>-->

<?php include("../includes/footer.php"); ?>

<style>
/* Low-stock row styling */
.inventory-table tbody tr.low-stock-row {
    background-color: #fdecea;
    color: #c0392b;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Hover effect (unchanged) */
.inventory-table tbody tr.low-stock-row:hover {
    background-color: #eef5ff;
    box-shadow: inset 4px 0 0 #2980b9;
    cursor: pointer;
}
</style>

<script>
// Sell Modal functions
function openSellModal(id, name, price) {
    document.getElementById('sellProductId').value = id;
    document.getElementById('sellProductName').innerText = name;
    document.getElementById('sellProductPrice').innerText = price;
    document.getElementById('sellModal').style.display = 'block';
}

function closeSellModal() {
    document.getElementById('sellModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('sellModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Clear search on page back
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
    }
});

// Live search functionality
document.getElementById('searchInput').addEventListener('keyup', function () {
    let query = this.value.trim();
    let resultsDiv = document.getElementById('searchResults');

    // If empty, clear results
    if (query === '') {
        resultsDiv.innerHTML = '';
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open('GET', '../includes/live-search.php?q=' + encodeURIComponent(query), true);
    
    xhr.onload = function () {
        if (xhr.status === 200) {
            resultsDiv.innerHTML = xhr.responseText;
        }
    };

    xhr.send();
});
</script>