<?php
require 'db_connection.php'; 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); 
}

$username = $_SESSION['username'];

function fetchTravelHistory($conn, $username) {
    $stmt = $conn->prepare("SELECT place, date FROM history WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $travelHistory = [];

    while ($row = $result->fetch_assoc()) {
        $travelHistory[] = $row;
    }
    $stmt->close();
    return $travelHistory;
}

function fetchNextVisit($conn, $username) {
    $stmt = $conn->prepare("SELECT nextVisit FROM nextplace WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $nextVisit = 'Not set'; 

    if ($row = $result->fetch_assoc()) {
        $nextVisit = $row['nextVisit'];
    }
    $stmt->close();
    return $nextVisit;
}

$travelHistory = fetchTravelHistory($conn, $username);
$nextVisit = fetchNextVisit($conn, $username);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['next_visit'])) {
    $nextVisit = $_POST['next_visit'];

    $checkStmt = $conn->prepare("SELECT * FROM nextplace WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE nextplace SET nextVisit = ? WHERE username = ?");
        $updateStmt->bind_param("ss", $nextVisit, $username);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        $insertStmt = $conn->prepare("INSERT INTO nextplace (username, nextVisit) VALUES (?, ?)");
        $insertStmt->bind_param("ss", $username, $nextVisit);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $nextVisit = fetchNextVisit($conn, $username);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="history.css" rel="stylesheet">
</head>
<body>
    <div class="over"></div>
    <div class="container">
        <h1>Travel History</h1>

        <?php if (!empty($travelHistory)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Place</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($travelHistory as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['place']); ?></td>
                            <td><?php echo htmlspecialchars($history['date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No travel history available.</p>
        <?php endif; ?>

        <div class="next-visit">
            <h3>Next Place to Visit:</h3>
            <p><?php echo htmlspecialchars($nextVisit); ?></p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="next_visit" placeholder="Enter next place to visit" required>
                <button type="submit">Update Next Visit</button>
            </div>
        </form>

        <div class="button-container">
            <button onclick="window.location.href='profile-1.php'">Go Back</button>
        </div>
    </div>
</body>
</html>
