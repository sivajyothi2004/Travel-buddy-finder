<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sender'], $_POST['receiver'], $_POST['message'])) {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $sender, $receiver, $message);
    if ($stmt->execute()) {
        echo "Message sent successfully.";
    } else {
        echo "Failed to send message.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
