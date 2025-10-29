<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// 1. Fetch total products per category
$categoryQuery = "SELECT category, COUNT(*) AS total_products, SUM(quantity) AS total_quantity 
                  FROM products 
                  GROUP BY category 
                  ORDER BY category ASC";
$categoryResult = $conn->query($categoryQuery);

// 2. Fetch monthly total sales for current year
$monthlySales = array_fill(1, 12, 0);
$months = [];
$totalSales = [];

$salesQuery = "
    SELECT 
        MONTH(sale_date) AS month_num,
        SUM(quantity * price_per_unit) AS total_sales
    FROM transactions
    WHERE status = 'active' AND YEAR(sale_date) = YEAR(CURDATE())
    GROUP BY MONTH(sale_date)
    ORDER BY month_num ASC
";
$salesResult = $conn->query($salesQuery);

while ($row = $salesResult->fetch_assoc()) {
    $monthNum = (int)$row['month_num'];
    $monthlySales[$monthNum] = (float)$row['total_sales'];
}

// Prepare arrays for Chart.js
for ($i = 1; $i <= 12; $i++) {
    $months[] = date('F', mktime(0, 0, 0, $i, 1));
    $totalSales[] = $monthlySales[$i];
}

// Search handling: filter categories by search input
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = $conn->real_escape_string(trim($_GET['q']));
    $categoryResult = $conn->query("
        SELECT category, COUNT(*) AS total_products, SUM(quantity) AS total_quantity
        FROM products
        WHERE category LIKE '%$search%'
        GROUP BY category
        ORDER BY category ASC
    ");
} else {
    $categoryResult = $conn->query("
        SELECT category, COUNT(*) AS total_products, SUM(quantity) AS total_quantity
        FROM products
        GROUP BY category
        ORDER BY category ASC
    ");
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<style>
/* === Modern Chart Styling === */
.chart-card {
    background: linear-gradient(145deg, #ffffff, #f0f4f8);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-radius: 20px;
    padding: 30px;
    margin: 30px auto;
    max-width: 1200px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.chart-card:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

.chart-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 15px;
}

.chart-subtitle {
    text-align: center;
    color: #555;
    font-size: 14px;
    margin-bottom: 25px;
}
</style>

<!-- MAIN CONTENT -->
<div class="main-content inventory-page">
    <h2 class="page-title"><i class="fa-solid fa-chart-simple"></i> Reports & Analytics</h2>

    <!-- Stock Summary by Category -->
    <h3 class="section-title">Stock Summary by Category</h3>
    <?php if ($categoryResult->num_rows > 0): ?>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Products</th>
                <th>Total Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $categoryResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo $row['total_products']; ?></td>
                <td><?php echo $row['total_quantity']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No categories found.</p>
    <?php endif; ?>


    <!-- Monthly Total Sales Chart -->
    <div class="chart-card">
        <div class="chart-title">💰 Monthly Total Sales</div>
        <div class="chart-subtitle">Visualize your total sales trends throughout the year.</div>
        <canvas id="totalSalesChart" style="width:100%; height:500px;"></canvas>
    </div>
</div>

<!-- Chart.js Setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('totalSalesChart').getContext('2d');

// Gradient background
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(75, 192, 192, 0.4)');
gradient.addColorStop(1, 'rgba(255, 255, 255, 0.1)');

const totalSalesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Total Sales (₱)',
            data: <?php echo json_encode($totalSales, JSON_NUMERIC_CHECK); ?>,
            borderColor: '#3b82f6',
            backgroundColor: gradient,
            borderWidth: 3,
            fill: true,
            tension: 0.35,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#1d4ed8',
            pointHoverBackgroundColor: '#2563eb',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1800,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                display: true,
                labels: {
                    color: '#1e293b',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            title: {
                display: true,
                text: 'Total Sales Trends - ' + new Date().getFullYear(),
                color: '#0f172a',
                font: {
                    size: 20,
                    weight: 'bold'
                },
                padding: {
                    top: 10,
                    bottom: 30
                }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#ffffff',
                bodyColor: '#e2e8f0',
                cornerRadius: 8,
                padding: 12,
                callbacks: {
                    label: function(context) {
                        return ' ₱' + context.formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Month',
                    color: '#1e293b',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                ticks: {
                    color: '#334155',
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Sales (₱)',
                    color: '#1e293b',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                ticks: {
                    color: '#334155',
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                },
                grid: {
                    color: 'rgba(203, 213, 225, 0.3)'
                }
            }
        }
    }
});
</script>

<?php include("../includes/footer.php"); ?>

<!-- Clear navbar search input on back/forward navigation -->
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.value = '';
        }
        window.location.href = window.location.pathname;
    }
});
</script>