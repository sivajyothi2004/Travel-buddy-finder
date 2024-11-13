<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="people.css" rel="stylesheet">
    <title>Buddies</title>
    <style>
        .Frame {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="head">
            <h1>Buddies for <span id="destination"></span></h1>
        </div>
        <div id="travelersList"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const travelers = JSON.parse(localStorage.getItem('buddies') || '[]');
            const destination = localStorage.getItem('searchDestination') || "Unknown";
            document.getElementById("destination").innerText = destination;

            const travelersList = document.getElementById('travelersList');
            if (travelers.length > 0) {
                travelers.forEach(traveler => {
                    const frame = document.createElement('div');
                    frame.className = 'Frame';
                    frame.innerHTML = `
                        <img src="data:image/jpeg;base64,${traveler.picture}" alt="profile-pic" />
                        <div class="quote">${traveler.quote} - ${traveler.username}</div>
                    `;

                    // Add click event to open chat with the traveler
                    frame.onclick = () => {
                        window.location.href = `chat.php?username=${traveler.username}`;
                    };

                    travelersList.appendChild(frame);
                });
            } else {
                travelersList.innerHTML = "<p>No travelers are going there.</p>";
            }
        });
    </script>
</body>
</html>
