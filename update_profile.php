<?php
require 'db_connection.php'; 

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); 
}

$username = $_SESSION['username'];

// Prepare and execute the SQL statement
$stmt = $conn->prepare("SELECT r.fullname, r.phone, r.picture, r.id, e.age, e.place 
                        FROM register r 
                        JOIN extra e ON r.username = e.username 
                        WHERE r.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user data was retrieved
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullname = $user['fullname'];
    $phone = $user['phone'];
    $governmentID = $user['id']; 
    $age = $user['age'];
    $place = $user['place'];

    $profilePicture = $user['picture'] ?: "default-profile.jpg"; // Use default if picture is empty
} else {
    // Default values if no user found
    $fullname = $phone = $governmentID = $age = $place = "Not found";
    $profilePicture = "default-profile.jpg"; 
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="detail.css">
    <title><?php echo htmlspecialchars($fullname); ?>'s Profile</title>
</head>
<body>
    <div class="tot-parent">
        <div class="heading"><h1>Details</h1></div>
        <div class="parent">
            <div class="prof1">
                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="profile-pic">
            </div>
            <div class="prof2">
                <div class="prof2-1">
                    <div class="det">
                        Name: <span class="retrieved-data"><?php echo htmlspecialchars($fullname); ?></span>
                    </div>
                    <div class="det">
                        Age: <span class="retrieved-data"><?php echo htmlspecialchars($age); ?></span>
                    </div>
                    <div class="det">
                        Place: <span class="retrieved-data"><?php echo htmlspecialchars($place); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="extra">
            <div class="bottom">
                <div class="det">
                    Contact No: <span class="retrieved-data"><?php echo htmlspecialchars($phone); ?></span>
                </div>
                <div class="det">
                    Government ID: 
                    <?php if ($governmentID): ?>
                        <a href="data:application/pdf;base64,<?php echo base64_encode($governmentID); ?>" download="gov_id.pdf">Download ID</a>
                    <?php else: ?>
                        <span class="retrieved-data">ID not available</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="cont">
            <div class="go-back">
                <button onclick="goBack()">Go Back</button>
            </div>
            <div class="go-back">
                <button onclick="window.location.href='edit_profile.php'">Edit</button>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
