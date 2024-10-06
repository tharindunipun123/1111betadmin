<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$round = isset($_GET['round']) ? intval($_GET['round']) : null;
$betPercentage = $round ? getBetPercentageForRound($conn, $round) : getBetPercentage($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Percentage</title>
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
                <li class="nav-item active">
                    <a class="nav-link" href="bet_percentage.php">Bet Percentage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profit_loss.php">Profit/Loss</a>
                </li>
                <li class="nav-item">
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
        <h1 class="mb-4">Bet Percentage</h1>

        <form class="mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label class="sr-only" for="round">Round</label>
                    <input type="number" class="form-control mb-2" id="round" name="round" placeholder="Enter round number" value="<?php echo $round; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Filter</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Bet Percentage Chart</div>
                    <div class="card-body">
                        <canvas id="betPercentageChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Bet Percentage Table</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Multiplier</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($betPercentage as $multiplier => $percentage): ?>
                                <tr>
                                    <td><?php echo $multiplier; ?></td>
                                    <td><?php echo $percentage; ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('betPercentageChart').getContext('2d');
        var betPercentageChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($betPercentage)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($betPercentage)); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Bet Percentage by Multiplier'
                }
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>