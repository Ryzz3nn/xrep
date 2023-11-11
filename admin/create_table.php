<?php
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

// SQL to create steam_users table
$sql = "CREATE TABLE IF NOT EXISTS steam_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  steam_id VARCHAR(255) NOT NULL,
  notes TEXT
) ENGINE=InnoDB;";

// Execute the query for steam_users
if ($conn->query($sql) === TRUE) {
    echo "Table steam_users created successfully";
} else {
    echo "Error creating table steam_users: " . $conn->error;
}

// SQL to create games table
$sql = "CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES steam_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";

// Execute the query for games
if ($conn->query($sql) === TRUE) {
    echo "Table games created successfully";
} else {
    echo "Error creating table games: " . $conn->error;
}

// Close the connection
$conn->close();
?>
