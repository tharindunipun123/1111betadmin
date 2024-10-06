<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$round = isset($_GET['round']) ? intval($_GET['round']) : null;
$roundInfo = $round ? getDetailedRoundInfo($conn, $round) : array();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Round Information</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">Reports</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="round_info.php">Round Information</a>
                </li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Round Information</h1>

        <form class="mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label class="sr-only" for="round">Round</label>
                    <input type="number" class="form-control mb-2" id="round" name="round" placeholder="Enter round number" value="<?php echo $round; ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">View Round</button>
                </div>
            </div>
        </form>

        <?php if ($round && !empty($roundInfo)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    Round <?php echo $round; ?> Information
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Multiplier</th>
                                <th>Bet Count</th>
                                <th>Total Amount</th>
                                <th>Payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roundInfo as $info): ?>
                            <tr>
                                <td><?php echo $info['multiplier']; ?></td>
                                <td><?php echo $info['bet_count']; ?></td>
                                <td>$<?php echo number_format($info['total_amount'], 2); ?></td>
                                <td>$<?php echo number_format($info['payout'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p class="mt-3">
                        <strong>Winning Multiplier:</strong> <?php echo $roundInfo[0]['winning_multiplier']; ?>
                    </p>
                </div>
            </div>
        <?php elseif ($round): ?>
            <div class="alert alert-warning">No information found for round <?php echo $round; ?>.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>