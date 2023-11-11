<?php
// wipe_data.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbHost     = '65.21.25.79';
$dbUsername = 'bajsapa';
$dbPassword = 'bajsapa123';
$dbName     = 'test123';
$dbPort     = '27003'; // Custom port for MariaDB

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Begin transaction
    $conn->begin_transaction();

    // Delete all rows from the games table
    $conn->query("DELETE FROM games");

    // Delete all rows from the steam_users table
    $conn->query("DELETE FROM steam_users");

    // Enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    // Commit transaction
    $conn->commit();

    echo "All data wiped successfully!";
} catch (mysqli_sql_exception $exception) {
    // Rollback the transaction on error
    $conn->rollback();
    echo "Error wiping data: " . $exception->getMessage();
}

// Close the connection
$conn->close();
?>
