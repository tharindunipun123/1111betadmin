<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$reportType = isset($_GET['type']) ? $_GET['type'] : 'daily';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Function to generate report data based on type and date
function generateReport($conn, $type, $date) {
    switch ($type) {
        case 'hourly':
            $sql = "SELECT HOUR(created_at) as hour, COUNT(*) as total_bets, SUM(amount) as total_amount
                    FROM betting_results
                    WHERE DATE(created_at) = ?
                    GROUP BY HOUR(created_at)
                    ORDER BY HOUR(created_at)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $date);
            break;
        case 'daily':
            $sql = "SELECT DATE(created_at) as date, COUNT(*) as total_bets, SUM(amount) as total_amount
                    FROM betting_results
                    WHERE YEAR(created_at) = YEAR(?) AND MONTH(created_at) = MONTH(?)
                    GROUP BY DATE(created_at)
                    ORDER BY DATE(created_at)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $date, $date);
            break;
        case 'weekly':
            $sql = "SELECT YEARWEEK(created_at) as week, COUNT(*) as total_bets, SUM(amount) as total_amount
                    FROM betting_results
                    WHERE YEAR(created_at) = YEAR(?)
                    GROUP BY YEARWEEK(created_at)
                    ORDER BY YEARWEEK(created_at)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $date);
            break;
        default:
            return array();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $report = array();
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
    return $report;
}

$reportData = generateReport($conn, $reportType, $date);

// ... (rest of the HTML code remains the same)
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bet_percentage.php">Bet Percentage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profit_loss.php">Profit/Loss</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="reports.php">Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="round_info.php">Round Information</a>
                </li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Reports</h1>

        <form class="mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <select class="form-control mb-2" id="type" name="type">
                        <option value="hourly" <?php echo $reportType == 'hourly' ? 'selected' : ''; ?>>Hourly</option>
                        <option value="daily" <?php echo $reportType == 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $reportType == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" class="form-control mb-2" id="date" name="date" value="<?php echo $date; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Generate Report</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Report Chart</div>
                    <div class="card-body">
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Report Table</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo ucfirst($reportType); ?></th>
                                    <th>Total Bets</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <td><?php echo $row[$reportType == 'hourly' ? 'hour' : ($reportType == 'daily' ? 'date' : 'week')]; ?></td>
                                    <td><?php echo $row['total_bets']; ?></td>
                                    <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="export_report.php?type=<?php echo $reportType; ?>&date=<?php echo $date; ?>" class="btn btn-success">Export Report</a>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('reportChart').getContext('2d');
        var reportChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($reportData, $reportType == 'hourly' ? 'hour' : ($reportType == 'daily' ? 'date' : 'week'))); ?>,
                datasets: [{
                    label: 'Total Bets',
                    data: <?php echo json_encode(array_column($reportData, 'total_bets')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Total Amount',
                    data: <?php echo json_encode(array_column($reportData, 'total_amount')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>