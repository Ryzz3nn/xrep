<?php
// Start the session at the beginning of the script.
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the log file name.
$logFile = 'debug_log.txt';

function writeToLog($message, $logFile) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

// Check if there's debugging info passed from lookup.php.
if (isset($_SESSION['debug_info'])) {
    writeToLog($_SESSION['debug_info'], $logFile);
    // Display the debug info or do something with it.
    echo $_SESSION['debug_info'];
    // Clear the debug info after logging.
    unset($_SESSION['debug_info']); 
}

// Check if there are search results passed from lookup.php.
$users = isset($_SESSION['search_results']) ? $_SESSION['search_results'] : [];
// Clear the search results after they have been retrieved.
unset($_SESSION['search_results']);

// Database credentials.
$dbHost     = '65.21.25.79';
$dbUsername = 'bajsapa';
$dbPassword = 'bajsapa123';
$dbName     = 'test123';
$dbPort     = '27003';

// Create database connection.
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

// Check connection.
if ($conn->connect_error) {
    $_SESSION['debug_info'] = "Connection failed: " . $conn->connect_error;
    writeToLog($_SESSION['debug_info'], $logFile);
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lookup'])) {
    $searchTerm = trim($_POST['searchTerm']);

    // Prepare the SQL statement to prevent SQL injection.
    $stmt = $conn->prepare("SELECT * FROM steam_users WHERE name = ? OR steam_id = ?");
    if ($stmt === false) {
        $_SESSION['debug_info'] = "Prepare failed: " . $conn->error;
        writeToLog($_SESSION['debug_info'], $logFile);
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute the statement.
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    if (!$stmt->execute()) {
        $_SESSION['debug_info'] = "Execute failed: " . $stmt->error;
        writeToLog($_SESSION['debug_info'], $logFile);
        die("Execute failed: " . $stmt->error);
    }

    // Get the result set from the prepared statement.
    $result = $stmt->get_result();
    if ($result === false) {
        $_SESSION['debug_info'] = "Get result set failed: " . $stmt->error;
        writeToLog($_SESSION['debug_info'], $logFile);
        die("Get result set failed: " . $stmt->error);
    }

    // Check for the number of rows that match the query.
    if ($result->num_rows > 0) {
        // Fetch the results and store them in the session.
        $_SESSION['search_results'] = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $_SESSION['debug_info'] = "0 results found for the search term: $searchTerm";
        writeToLog($_SESSION['debug_info'], $logFile);
    }

    $stmt->close();
} else {
    $_SESSION['debug_info'] = "No search term submitted";
    writeToLog($_SESSION['debug_info'], $logFile);
}

$conn->close();

// Redirect to userdisplay.php.
header('Location: userdisplay.php');
exit();
?>