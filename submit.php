<?php
// submit.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$dbHost     = '65.21.25.79';
$dbUsername = 'bajsapa';
$dbPassword = 'bajsapa123';
$dbName     = 'test123';
$dbPort     = '27003';

// Create a new database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form data is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $steamName = $_POST['steamName'];
    $steam64ID = $_POST['steam64ID'];
    $notes = $_POST['notes']; // Assume 'notes' is part of your form
    // Add other form data as needed

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check if a user with the given steam_id already exists
        $checkStmt = $conn->prepare("SELECT id FROM steam_users WHERE steam_id = ?");
        $checkStmt->bind_param("s", $steam64ID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $existingUser = $result->fetch_assoc();
        $checkStmt->close();

        if ($existingUser) {
            // User exists, update the existing record
            $updateStmt = $conn->prepare("UPDATE steam_users SET name = ?, notes = ? WHERE steam_id = ?");
            $updateStmt->bind_param("sss", $steamName, $notes, $steam64ID);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // No user exists, insert a new record
            $insertStmt = $conn->prepare("INSERT INTO steam_users (name, steam_id, notes) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $steamName, $steam64ID, $notes);
            $insertStmt->execute();
            $insertStmt->close();
        }

        // Commit transaction
        $conn->commit();

        echo "User data processed successfully!";
    } catch (mysqli_sql_exception $exception) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error processing user data: " . $exception->getMessage();
    }
}

// Close the connection
$conn->close();
?>
