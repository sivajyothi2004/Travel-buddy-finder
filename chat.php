<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php';

$loggedInUser = $_SESSION['username'];
$otherUsername = $_GET['username'];

// Fetch chat partner’s details
$query = $conn->prepare("
    SELECT register.username, register.picture, extra.quote 
    FROM register 
    JOIN extra ON register.username = extra.username 
    WHERE register.username = ?
");
$query->bind_param("s", $otherUsername);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $profilePicture = $user['picture'];
    $quote = $user['quote'];
} else {
    echo "User not found.";
    exit();
}
$query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($otherUsername) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="chat.css">
    <style>
        .chat-header {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        .chat-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .chat-header .user-info {
            display: flex;
            flex-direction: column;
        }
        .chat-header .user-info .username {
            font-weight: bold;
            font-size: 1.2em;
        }
        .chat-header .user-info .quote {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <img src="data:image/jpeg;base64,<?= base64_encode($profilePicture) ?>" alt="Profile Picture" />
            <div class="user-info">
                <span class="username">Chat with <?= htmlspecialchars($otherUsername) ?></span>
                <span class="quote"><?= htmlspecialchars($quote) ?></span>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages"></div>
        <div class="message-input">
            <input type="text" id="messageInput" placeholder="Type your message..." />
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');

        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `messages_received.php?username=<?= urlencode($otherUsername) ?>`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    chatMessages.innerHTML = xhr.responseText;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            };
            xhr.send();
        }

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const messageText = input.value.trim();

            if (messageText === "") {
                alert("Please enter a message.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    input.value = "";
                    fetchMessages(); // Refresh messages after sending
                } else if (xhr.readyState === 4) {
                    alert("Failed to send message.");
                }
            };
            xhr.send(`message=${encodeURIComponent(messageText)}&receiver=<?= urlencode($otherUsername) ?>`);
        }

        // Periodically fetch messages every 5 seconds
        setInterval(fetchMessages, 5000);
        fetchMessages(); 
    </script>
</body>
</html>
