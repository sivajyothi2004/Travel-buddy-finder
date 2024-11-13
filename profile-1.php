<?php
require 'db_connection.php'; 

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit(); 
}

$profilePicture = "profile.png"; 
$username = "Guest";
$nameInQuote = "Traveler";
$age = "Unknown";
$place = "Unknown";
$quoteText = "Life isn’t meant to be lived in one place. Explore, embrace the unknown, and remember: it’s not that serious—just go, just do it."; // Default quote

$loggedInUser = $_SESSION['username'];

// Fetch user details
$stmt = $conn->prepare("SELECT r.username, r.picture, r.fullname, e.age, e.place, e.quote 
                        FROM register r 
                        JOIN extra e ON r.username = e.username 
                        WHERE r.username = ?");
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $profilePicture = $user['picture']; 
    $nameInQuote = $user['fullname']; 
    $age = $user['age'];
    $place = $user['place'];
    $quote = $user['quote']; 

    $quoteText = $quote ?: $quoteText; 

    if (!empty($profilePicture)) {
        $imageType = 'image/jpeg'; 
        if (strpos($profilePicture, 'png') !== false) {
            $imageType = 'image/png';
        }
        $profilePicture = 'data:' . $imageType . ';base64,' . base64_encode($profilePicture);
    } else {
        $profilePicture = "default-profile.jpg"; 
    }
}

$stmt->close();

// Get total messages for notification (Optional: You can keep it or remove it)
$messageCountQuery = $conn->prepare("
    SELECT COUNT(*) as message_count 
    FROM messages 
    WHERE receiver = ?
");
$messageCountQuery->bind_param("s", $loggedInUser);
$messageCountQuery->execute();
$messageCountResult = $messageCountQuery->get_result();
$messageCountData = $messageCountResult->fetch_assoc();
$messageCount = $messageCountData['message_count'] ?? 0; 
$messageCountQuery->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">
    <title><?php echo htmlspecialchars($username); ?>'s Profile</title>
</head>
<body>  
    <div class="navbar">
        <div class="menu-options">
            <div class="menu-option" onclick="viewNotifications()">🔔 Notifications <?php if ($messageCount > 0) { echo "<span class='notification-indicator'>($messageCount)</span>"; } ?></div>
            <div class="menu-option" onclick="viewMessages()">✉️ Messages</div>
        </div>
        <div class="logout">
            <button onclick="logout()">Logout</button>
        </div>
    </div>
    
    <div class="parent">
        <div class="over">
            <div class="profile-pic">
                <ul id="Frames">
                    <li class="Frame">
                        <a href="#">
                            <img src="<?php echo $profilePicture; ?>" alt="profile-pic" />
                        </a>
                        <div class="quote">
                            <?php echo htmlspecialchars($quoteText); ?><br>
                            - <?php echo htmlspecialchars($nameInQuote); ?>
                        </div>
                    </li>
                </ul>
            </div>        
            <div class="details">
                <div class="line">
                    <h1>Welcome, <?php echo htmlspecialchars($nameInQuote); ?>!</h1>
                    <h1>Connect with fellow travelers and start your next adventure together!</h1>
                </div>
                <div class="but-par">
                    <button onclick="window.location.href='history.php'">Travel History</button>
                    <button onclick="goToDetails()">Details</button>
                </div>
                <div class="but-1">
                    <button onclick="toggleSearch()">Find a buddy</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form (Modal-style) -->
    <div class="search-bar" id="searchBar">
        <span class="close-btn" onclick="toggleSearch()">×</span>
        <form class="example" onsubmit="return searchDestination(event)">
            <input type="text" id="search" placeholder="Search for a travel buddy..." />
            <button type="submit">Search</button>
        </form>
    </div>

    <script>
        function logout() {
            window.location.href = "logout.php"; 
        }

        function searchDestination(event) {
            event.preventDefault();

            var searchTerm = document.getElementById('search').value.toLowerCase();

            fetch('search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ place: searchTerm })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.results.length > 0) {
                    localStorage.setItem('buddies', JSON.stringify(data.results));
                    localStorage.setItem('searchDestination', searchTerm);
                    window.location.href = 'people2.php';
                } else {
                    alert(data.message || "No buddies found for this location.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
            });

            return false;
        }

        function toggleSearch() {
            var searchBar = document.getElementById("searchBar");
            searchBar.classList.toggle("active");
        }

        function viewNotifications() {
            window.location.href = "notifications.php"; // Redirect to notifications page
        }

        function viewMessages() {
            window.location.href = "messages_received.php"; // Redirect to messages page
        }

        function goToDetails() {
            window.location.href = "detail.php"; // Redirect to detail.php
        }
    </script>
</body>
</html>
