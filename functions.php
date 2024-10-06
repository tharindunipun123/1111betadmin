<?php
function getTotalBets($conn) {
    $sql = "SELECT COUNT(*) as total FROM betting_results";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getBetPercentage($conn) {
    $sql = "SELECT multiplier, COUNT(*) as count FROM betting_results GROUP BY multiplier";
    $result = $conn->query($sql);
    $betPercentage = array();
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $betPercentage[$row['multiplier']] = $row['count'];
        $total += $row['count'];
    }
    foreach ($betPercentage as $multiplier => $count) {
        $betPercentage[$multiplier] = round(($count / $total) * 100, 2);
    }
    return $betPercentage;
}

function getProfitLoss($conn, $round = null) {
    $whereClause = $round ? "WHERE br.round = ?" : "";
    $sql = "SELECT 
                SUM(br.amount) as total_bets,
                SUM(CASE WHEN br.multiplier = r.winning_multiplier THEN br.amount * br.multiplier ELSE 0 END) as total_payouts,
                SUM(CASE WHEN br.multiplier != r.winning_multiplier THEN br.amount ELSE 0 END) as house_winnings,
                SUM(br.amount) - SUM(CASE WHEN br.multiplier = r.winning_multiplier THEN br.amount * br.multiplier ELSE 0 END) as net_profit_loss
            FROM betting_results br
            JOIN rounds r ON br.round = r.round_number
            $whereClause";
    
    $stmt = $conn->prepare($sql);
    if ($round) {
        $stmt->bind_param("i", $round);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getRoundResults($conn) {
    $sql = "SELECT round_number, winning_multiplier FROM rounds ORDER BY id DESC LIMIT 10";
    $result = $conn->query($sql);
    $roundResults = array();
    while ($row = $result->fetch_assoc()) {
        $roundResults[] = $row;
    }
    return $roundResults;
}

function getBetPercentageForRound($conn, $round) {
    $sql = "SELECT multiplier, COUNT(*) as count FROM betting_results WHERE round = ? GROUP BY multiplier";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $round);
    $stmt->execute();
    $result = $stmt->get_result();
    $betPercentage = array();
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $betPercentage[$row['multiplier']] = $row['count'];
        $total += $row['count'];
    }
    foreach ($betPercentage as $multiplier => $count) {
        $betPercentage[$multiplier] = round(($count / $total) * 100, 2);
    }
    return $betPercentage;
}

function getProfitLossForRound($conn, $round) {
    $sql = "SELECT SUM(CASE WHEN br.multiplier = r.winning_multiplier THEN br.amount * br.multiplier ELSE -br.amount END) as profit_loss 
            FROM betting_results br
            JOIN rounds r ON br.round = r.round_number
            WHERE br.round = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $round);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['profit_loss'];
}

function getDetailedRoundInfo($conn, $round) {
    $sql = "SELECT br.multiplier, COUNT(*) as bet_count, SUM(br.amount) as total_amount, 
            r.winning_multiplier, 
            SUM(CASE WHEN br.multiplier = r.winning_multiplier THEN br.amount * br.multiplier ELSE 0 END) as payout
            FROM betting_results br
            JOIN rounds r ON br.round = r.round_number
            WHERE br.round = ?
            GROUP BY br.multiplier";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $round);
    $stmt->execute();
    $result = $stmt->get_result();
    $roundInfo = array();
    while ($row = $result->fetch_assoc()) {
        $roundInfo[] = $row;
    }
    return $roundInfo;
}