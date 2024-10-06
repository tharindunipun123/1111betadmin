<?php
// Database configuration
$host = 'localhost';  // Usually 'localhost' for local development
$dbname = 'spin_wheel_db';
$username = 'root';  // Replace with your actual database username
$password = '';  // Replace with your actual database password

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}
 // Replace with your actual timezone, e.g., 'America/New_York'
?>