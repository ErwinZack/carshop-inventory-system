<?php
session_start();

// Redirect if not logged in or not an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../user/login.php");
    exit();
}

// Include database connection
include('../config/db.php');

// Fetch summary stats
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) AS count FROM products WHERE status = 'active'")->fetch_assoc()['count'] ?? 0;
// Fetch low-stock count (only active products)
$lowStock = $conn->query("SELECT COUNT(*) AS count FROM products WHERE quantity < 10 AND status = 'active'")
                 ->fetch_assoc()['count'] ?? 0;

// Fetch total inventory value (only active products)
$totalValueQuery = $conn->query("SELECT SUM(price * quantity) AS total_value FROM products WHERE status = 'active'");
$totalValue = $totalValueQuery->fetch_assoc()['total_value'] ?? 0;

// Fetch total sales (sum of all active transactions)
$totalSalesQuery = $conn->query("SELECT SUM(quantity * price_per_unit) AS total_sales FROM transactions WHERE status = 'active'");
$totalSales = $totalSalesQuery->fetch_assoc()['total_sales'] ?? 0;

// Fetch Product Category Distribution (only active products)
$categoryQuery = $conn->query("SELECT category, COUNT(*) AS count FROM products WHERE status = 'active' GROUP BY category");
$categories = [];
$categoryCounts = [];
while ($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row['category'];
    $categoryCounts[] = (int)$row['count'];
}

// ✅ Fetch Low Stock Products (only active ones)
$lowStockQuery = $conn->query("SELECT name, quantity FROM products WHERE quantity <= 10 AND status = 'active' ORDER BY quantity ASC");
$productNames = [];
$quantities = [];
while ($row = $lowStockQuery->fetch_assoc()) {
    $productNames[] = $row['name'];
    $quantities[] = (int)$row['quantity'];
}


// Convert data to JSON for JavaScript
$categories_json = json_encode($categories);
$categoryCounts_json = json_encode($categoryCounts);
$productNames_json = json_encode($productNames);
$quantities_json = json_encode($quantities);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>
<?php include('../includes/sidebar.php'); ?>

<div class="main-content admin-dashboard">
    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>!</p>
    </div>

    <div class="stats-cards">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-details">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card products">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="stat-details">
                <h3><?php echo $totalProducts; ?></h3>
                <p>Total Products</p>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-details">
                <h3><?php echo $lowStock; ?></h3>
                <p>Low Stock Items</p>
            </div>
        </div>

        <div class="stat-card sales">
            <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-details">
                <h3>₱<?php echo number_format($totalSales, 0); ?></h3>
                <p>Total Sales</p>
            </div>
        </div>

        <div class="stat-card value">
            <div class="stat-icon"><i class="fa-solid fa-peso-sign"></i></div>
            <div class="stat-details">
                <h3>₱<?php echo number_format($totalValue, 0); ?></h3>
                <p>Total Inventory Value</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <h3><i class="fa-solid fa-chart-simple"></i> Dashboard Analytics</h3>
    <div class="charts-section">
        <!-- Product Category Pie/Donut Chart -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-boxes-stacked"></i> Products by Category</h3>
            <div id="categoryPieChart" style="width: 100%; height: 350px;"></div>
        </div>

        <!-- Low Stock Products Bar Chart -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Products</h3>
            <canvas id="lowStockChart"></canvas>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<!-- Chart Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
// Google Charts for Product Category

google.charts.load("current", {
    packages: ["corechart"]
});
google.charts.setOnLoadCallback(drawCategoryChart);

function drawCategoryChart() {
    var dataArray = [
        ['Category', 'Number of Products']
    ];
    var categories = <?php echo $categories_json; ?>;
    var counts = <?php echo $categoryCounts_json; ?>;

    for (var i = 0; i < categories.length; i++) {
        dataArray.push([categories[i], counts[i]]);
    }

    var data = google.visualization.arrayToDataTable(dataArray);

    var options = {
        title: 'Products by Category',
        pieHole: 0.4,
        legend: {
            position: 'bottom',
            alignment: 'center'
        },
        slices: {
            0: {
                color: '#3374E6'
            },
            1: {
                color: '#E63926'
            },
            2: {
                color: '#FFA500'
            },
            3: {
                color: '#14A44D'
            },
            4: {
                color: '#8E24AA'
            },
            5: {
                color: '#2A9D8F'
            },
            6: {
                color: '#F4A261'
            },
            7: {
                color: '#264653'
            }
        },
        chartArea: {
            width: '90%',
            height: '80%'
        },
        titleTextStyle: {
            fontSize: 16,
            bold: true
        }
    };

    var chart = new google.visualization.PieChart(document.getElementById('categoryPieChart'));
    chart.draw(data, options);
}


// Chart.js for Low Stock Products

const lowStockCtx = document.getElementById('lowStockChart').getContext('2d');
const lowStockChart = new Chart(lowStockCtx, {
    type: 'bar',
    data: {
        labels: <?php echo $productNames_json; ?>,
        datasets: [{
            label: 'Quantity',
            data: <?php echo $quantities_json; ?>,
            backgroundColor: '#E63926',
            borderRadius: 6,
            barPercentage: 0.6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantity'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Product'
                }
            }
        }
    }
});
</script>

<style>
.charts-section {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    /* margin-top: 2rem; */
    justify-content: center;
}

.chart-card {
    flex: 1 1 450px;
    max-width: 600px;
    min-width: 400px;
    background: #fff;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    text-align: center;
    transition: transform 0.2s;
    height: 400px;
}

.chart-card:hover {
    transform: translateY(-5px);
}

.chart-card h3 {
    margin-bottom: 1rem;
    font-size: 1.2rem;
    color: #333;
}

.chart-card canvas {
    width: 100% !important;
    height: 300px !important;
}
</style>