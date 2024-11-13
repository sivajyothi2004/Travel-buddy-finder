<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php';

$loggedInUser = $_SESSION['username'];

// Query to fetch messages where the logged-in user is the receiver or sender
$messagesQuery = $conn->prepare("
    SELECT sender, receiver, message, timestamp 
    FROM messages 
    WHERE receiver = ? OR sender = ? 
    ORDER BY timestamp DESC
");
$messagesQuery->bind_param("ss", $loggedInUser, $loggedInUser);
$messagesQuery->execute();
$messagesResult = $messagesQuery->get_result();
$messages = $messagesResult->fetch_all(MYSQLI_ASSOC);
$messagesQuery->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <div class="messages-container">
        <h2>Chat</h2>
        <div class="messages-list">
            <?php if (empty($messages)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['receiver'] === $loggedInUser ? 'received' : 'sent' ?>">
                        <div class="message-header">
                            <strong><?= htmlspecialchars($msg['sender']) ?></strong>
                            <span class="timestamp"><?= date('Y-m-d H:i', strtotime($msg['timestamp'])) ?></span>
                        </div>
                        <p><?= htmlspecialchars($msg['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Message Sending Form -->
        <div class="message-input">
            <input type="text" id="messageInput" placeholder="Type your message..." />
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const messageText = input.value.trim();

            if (messageText === "") {
                alert("Please enter a message.");
                return;
            }

            const recipient = prompt('Enter the username of the recipient:');
            if (!recipient) {
                alert("Recipient username is required.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Clear the input field after sending
                    input.value = "";
                    location.reload();
                } else if (xhr.readyState === 4) {
                    alert("Failed to send message.");
                }
            };
            xhr.send(`sender=<?= urlencode($loggedInUser) ?>&receiver=${encodeURIComponent(recipient)}&message=${encodeURIComponent(messageText)}`);
        }
    </script>
</body>
</html>
