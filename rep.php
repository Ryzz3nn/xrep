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

// SQL to create reputation table
$sql = "CREATE TABLE IF NOT EXISTS reputation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_name VARCHAR(255) NOT NULL,
    given_by VARCHAR(255) NOT NULL,
    reputation_points INT DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES steam_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";

// Execute the query to create the table
if ($conn->query($sql) === TRUE) {
    echo "Table reputation created successfully";
} else {
    echo "Error creating table reputation: " . $conn->error;
}

$conn->close();
?>
