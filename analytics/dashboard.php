<?php
require_once 'config.php';
session_start();

// Basic authentication (you should implement proper authentication)
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    die("Connection failed");
}

// Get date range from query parameters or use default (last 30 days)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Fetch analytics data
function getPageViews($conn, $startDate, $endDate) {
    $stmt = $conn->prepare("
        SELECT DATE(timestamp) as date, COUNT(*) as count
        FROM page_views
        WHERE DATE(timestamp) BETWEEN ? AND ?
        GROUP BY DATE(timestamp)
        ORDER BY date
    ");
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTopPages($conn, $startDate, $endDate) {
    $stmt = $conn->prepare("
        SELECT page_url, COUNT(*) as views
        FROM page_views
        WHERE DATE(timestamp) BETWEEN ? AND ?
        GROUP BY page_url
        ORDER BY views DESC
        LIMIT 10
    ");
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFormSubmissions($conn, $startDate, $endDate) {
    $stmt = $conn->prepare("
        SELECT form_type, COUNT(*) as submissions,
        SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful
        FROM form_submissions
        WHERE DATE(timestamp) BETWEEN ? AND ?
        GROUP BY form_type
    ");
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageViews = getPageViews($conn, $startDate, $endDate);
$topPages = getTopPages($conn, $startDate, $endDate);
$formSubmissions = getFormSubmissions($conn, $startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Adile Diagnostics</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .chart-container {
            height: 300px;
            margin-top: 1rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .data-table th,
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .date-filter {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .date-filter input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Analytics Dashboard</h1>
        
        <form class="date-filter">
            <label>
                Start Date:
                <input type="date" name="start_date" value="<?php echo $startDate; ?>">
            </label>
            <label>
                End Date:
                <input type="date" name="end_date" value="<?php echo $endDate; ?>">
            </label>
            <button type="submit" class="primary-button">Update</button>
        </form>
        
        <div class="dashboard-grid">
            <!-- Page Views Chart -->
            <div class="dashboard-card">
                <h2>Page Views</h2>
                <div class="chart-container">
                    <canvas id="pageViewsChart"></canvas>
                </div>
            </div>
            
            <!-- Top Pages -->
            <div class="dashboard-card">
                <h2>Top Pages</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPages as $page): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($page['page_url']); ?></td>
                            <td><?php echo $page['views']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Form Submissions -->
            <div class="dashboard-card">
                <h2>Form Submissions</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Form</th>
                            <th>Total</th>
                            <th>Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formSubmissions as $form): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($form['form_type']); ?></td>
                            <td><?php echo $form['submissions']; ?></td>
                            <td><?php echo round(($form['successful'] / $form['submissions']) * 100); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts
        const pageViewsCtx = document.getElementById('pageViewsChart').getContext('2d');
        new Chart(pageViewsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($pageViews, 'date')); ?>,
                datasets: [{
                    label: 'Page Views',
                    data: <?php echo json_encode(array_column($pageViews, 'count')); ?>,
                    borderColor: '#0984E3',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 