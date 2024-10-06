<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$round = isset($_GET['round']) ? intval($_GET['round']) : null;
$profitLossData = getProfitLoss($conn, $round);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit and Loss</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar code remains the same -->
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
        <h1 class="mb-4">Profit and Loss</h1>

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

        <div class="card">
    <div class="card-header">
        Profit/Loss Summary <?php echo $round ? "for Round $round" : "for All Rounds"; ?>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <tr>
                <th>Total Bets (Revenue)</th>
                <td>$<?php echo number_format($profitLossData['total_bets'], 2); ?></td>
            </tr>
            <tr>
                <th>Total Payouts (Expense)</th>
                <td>$<?php echo number_format($profitLossData['total_payouts'], 2); ?></td>
            </tr>
            <tr>
                <th>House Winnings (from losing bets)</th>
                <td>$<?php echo number_format($profitLossData['house_winnings'], 2); ?></td>
            </tr>
            <tr>
                <th>Net Profit/Loss</th>
                <td class="<?php echo $profitLossData['net_profit_loss'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                    $<?php echo number_format(abs($profitLossData['net_profit_loss']), 2); ?>
                    (<?php echo $profitLossData['net_profit_loss'] >= 0 ? 'Profit' : 'Loss'; ?>)
                </td>
            </tr>
        </table>
    </div>
</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>