<?php
require 'db_connection.php'; 

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); 
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT r.fullname, r.phone, r.picture, r.id, e.age, e.place, e.quote 
                        FROM register r 
                        JOIN extra e ON r.username = e.username 
                        WHERE r.username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullname = $user['fullname'];
    $phone = $user['phone'];
    $governmentID = $user['id']; 
    $age = $user['age'];
    $place = $user['place'];
    $quote = $user['quote'];
    $profilePicture = $user['picture'];
} else {
    $fullname = $phone = $governmentID = $age = $place = $quote = "Not found";
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
    <link rel="stylesheet" href="detail-ed.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="form-container">
        <div class="head">
            <h1>Edit Profile</h1>
        </div>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fullname">Name:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" required>
            </div>
            <div class="form-group">
                <label for="place">Place:</label>
                <input type="text" id="place" name="place" value="<?php echo htmlspecialchars($place); ?>" required>
            </div>
            <div class="form-group">
                <label for="quote">Quote:</label>
                <input type="text" id="quote" name="quote" value="<?php echo htmlspecialchars($quote); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="gov_id">Government ID (PDF):</label>
                <input type="file" id="gov_id" name="gov_id" accept=".pdf">
            </div>
            <div class="form-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
            </div>
            <div class="but">
                <button type="submit" class="save-btn">Save Changes</button>
            </div>
            <div class="but">
                <button type="button" class="save-btn" onclick="window.location.href='detail.php'">Go Back</button>
            </div>
        </form>
    </div>
</body>
</html>
