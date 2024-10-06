<?php
session_start();
require_once 'db_connection.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $round_number = $_POST['round_number'];
    $action = $_POST['action'];
    
    // INSERT action
    if ($action == 'insert') {
        $stmt = $conn->prepare("INSERT INTO manual_set (round_number) VALUES (?)");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("i", $round_number);
        $stmt->execute();
    } 
    // UPDATE action
    elseif ($action == 'update') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE manual_set SET round_number = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("ii", $round_number, $id);
        $stmt->execute();
    } 
    // DELETE action
    elseif ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM manual_set WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch all rows for display
$result = $conn->query("SELECT * FROM manual_set ORDER BY id DESC LIMIT 100");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Manage Round Numbers</title>
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
            <li class="nav-item">
                <a class="nav-link" href="round_info.php">Round Information</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="round_numbers.php">Manage Round Numbers</a>
            </li>
        </ul>
    </div>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Manage Round Numbers</h2>

    <!-- Form for Insert/Update/Delete -->
    <form method="post" class="mb-4">
        <div class="form-group">
            <label for="round_number">Round Number</label>
            <select name="round_number" id="round_number" class="form-control">
                <option value="2">2</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="7">7</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
        </div>
        <div class="form-group">
            <label for="id">ID (for update/delete)</label>
            <input type="number" name="id" id="id" class="form-control">
        </div>
        <button type="submit" name="action" value="insert" class="btn btn-success">Insert</button>
        <button type="submit" name="action" value="update" class="btn btn-warning">Update</button>
        <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
    </form>

    <!-- Display Existing Data -->
    <h3 class="mb-3">Existing Round Numbers (Last 100)</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Round Number</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['round_number']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>