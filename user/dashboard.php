<?php
session_start();

// Redirect if not logged in or not a User
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../user/login.php");
    exit();
}

// Include database connection
include('../config/db.php');

// Fetch summary stats (exclude archived products) 
$totalProducts = $conn->query("SELECT COUNT(*) AS count FROM products WHERE status = 'active'")
                     ->fetch_assoc()['count'] ?? 0;

$lowStock = $conn->query("SELECT COUNT(*) AS count FROM products WHERE quantity < 10 AND status = 'active'")
                 ->fetch_assoc()['count'] ?? 0;

$totalValueQuery = $conn->query("SELECT SUM(price * quantity) AS total_value FROM products WHERE status = 'active'");
$totalValue = ($totalValueQuery) ? ($totalValueQuery->fetch_assoc()['total_value'] ?? 0) : 0;

// ✅ FIXED: Total Sales (sum of all active transactions, regardless of product status)
$totalSalesQuery = $conn->query("
    SELECT SUM(t.quantity * t.price_per_unit) AS total_sales
    FROM transactions t
    WHERE t.status = 'active'
");
$totalSales = ($totalSalesQuery) ? ($totalSalesQuery->fetch_assoc()['total_sales'] ?? 0) : 0;

// Top selling products (exclude archived products) 
$topSellingQuery = $conn->query("
    SELECT p.name, SUM(t.quantity) AS total_sold
    FROM transactions t
    JOIN products p ON t.product_id = p.id
    WHERE t.status = 'active' AND p.status = 'active'
    GROUP BY t.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

// Build arrays safely (only if query succeeded)
$topProducts = [];
$topQuantities = [];

if ($topSellingQuery) {
    while ($row = $topSellingQuery->fetch_assoc()) {
        $topProducts[] = $row['name'];
        $topQuantities[] = (int)$row['total_sold'];
    }
}

// Convert to JSON for Chart.js (safe)
$topProductsJSON = json_encode($topProducts);
$topQuantitiesJSON = json_encode($topQuantities);
?>

<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>
<?php include('../includes/sidebar.php'); ?>

<!-- Main Dashboard -->
<div class="main-content user-dashboard">
    <div class="dashboard-header">
        <h2>User Dashboard</h2>
        <p>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card total">
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

        <!-- Total Sales -->
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

    <!-- Top Selling Products Chart -->
    <div class="chart-section">
        <h3><i class="fa-solid fa-chart-bar"></i> Top Selling Products</h3>
        <div class="chart-card" style="max-width: 1200px; margin: auto; height: 420px;">
            <canvas id="topSellingChart"></canvas>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('topSellingChart').getContext('2d');
    const topSellingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $topProductsJSON; ?>,
            datasets: [{
                label: 'Total Sold',
                data: <?php echo $topQuantitiesJSON; ?>,
                backgroundColor: [
                    '#3498db', '#1abc9c', '#e67e22', '#9b59b6', '#e74c3c'
                ],
                borderRadius: 8, // rounded bars
                barPercentage: 0.6, // thinner or thicker relative to category
                categoryPercentage: 0.7 // spacing between bars
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // allows custom height
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#34495e',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#555',
                        font: {
                            size: 14 // smaller numbers
                        }
                    },
                    grid: {
                        color: '#eee'
                    },
                    title: {
                        display: true,
                        text: 'Quantity Sold',
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold' // smaller axis titles
                        }
                    }
                },
                x: {
                    ticks: {
                        color: '#555',
                        font: {
                            size: 14 // smaller numbers
                        }
                    },
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Products',
                        color: '#333',
                        font: {
                            size: 18,
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
    </script>