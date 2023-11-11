<?php
// db.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbHost     = '65.21.25.79';
$dbUsername = 'bajsapa';
$dbPassword = 'bajsapa123';
$dbName     = 'test123';
$dbPort     = '27003'; // Custom port for MariaDB

// Create a new database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// We will include this file in other scripts to use $conn
?>
