<?php
// userdisplay.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbHost     = '65.21.25.79';
$dbUsername = 'bajsapa';
$dbPassword = 'bajsapa123';
$dbName     = 'test123';
$dbPort     = '27003';

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : '';
echo "Search Term: " . htmlspecialchars($searchTerm) . "<br>";

$users = []; 
if ($searchTerm !== '') {
    $searchTermLike = "%" . $conn->real_escape_string($searchTerm) . "%";
    $stmt = $conn->prepare("SELECT * FROM steam_users WHERE name LIKE ?");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    
    $stmt->bind_param("s", $searchTermLike);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        die("Get result set failed: " . htmlspecialchars($conn->error));
    }
    
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    foreach ($users as $index => $user) {
        $userId = $user['id'];
        $repStmt = $conn->prepare("SELECT game_name, given_by FROM reputation WHERE user_id = ?");
        if ($repStmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        $repStmt->bind_param("i", $userId);
        if (!$repStmt->execute()) {
            die("Execute failed: " . htmlspecialchars($repStmt->error));
        }
        
        $repResult = $repStmt->get_result();
        if ($repResult === false) {
            die("Get result set failed: " . htmlspecialchars($conn->error));
        }
        
        $reputationHistory = $repResult->fetch_all(MYSQLI_ASSOC);
        $users[$index]['reputation'] = $reputationHistory;
        
        $repStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .user-details {
            margin-top: 60px;
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: auto;
            margin-right: auto;
            box-shadow: -5px 5px 0px 0px rgba(156,29,43,1),
                        -10px 10px 0px 0px rgba(123,25,36,1),
                        -15px 15px 0px 0px rgba(91,19,27,1),
                        -20px 20px 0px 0px rgba(55,11,16,1),
                        -25px 25px 0px 0px rgba(28,6,8,1);
        }
        .user-info {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }
        .strong {
            color: #333;
            font-weight: bold;
        }
        .back-button {
            padding: 10px 20px;
            background-color: #9C1D2B; /* Change this color to your preferred button color */
            color: white; /* Change text color if needed */
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            display: block;
            width: fit-content;
            margin: 40px auto;
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        .back-button:hover {
            background-color: #5B131B; /* Change this color to your preferred hover color */
        }
    </style>
</head>
<body>
    <div class="user-details">
        <?php if (!empty($users)): ?>
            <h2>User Details</h2>
            <?php foreach ($users as $user): ?>
                <div class="user-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Steam ID:</strong> <?php echo htmlspecialchars($user['steam_id']); ?></p>
                    <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($user['notes'])); ?></p>
                    <h3>Reputation History:</h3>
                    <?php foreach ($user['reputation'] as $rep): ?>
                        <p>Game: <?php echo htmlspecialchars($rep['game_name']); ?></p>
                        <p>Given by: <?php echo htmlspecialchars($rep['given_by']); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php elseif ($searchTerm === ''): ?>
            <p>No search term provided. Please enter a search term.</p>
        <?php else: ?>
            <p>User not found. Please check the search term and try again.</p>
        <?php endif; ?>
    </div>
    <a href="javascript:history.back()" class="back-button">Go Back</a>
</body>
</html>