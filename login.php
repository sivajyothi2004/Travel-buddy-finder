<?php
session_start();

require 'db_connection.php';

$usernameError = $passwordError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    $stmt = $conn->prepare("SELECT * FROM register WHERE username = ?");
    if ($stmt === false) {
        error_log("SQL Error: " . $conn->error);
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stored_password_hash = $user['password'];

        if (password_verify($password, $stored_password_hash)) {
            $_SESSION['username'] = $user['username']; 
            header("Location: profile-1.php"); 
            exit();
        } else {
            $usernameError = "Incorrect username or password";
            $passwordError = "Incorrect username or password";
            error_log("Password verification failed for user: $username");
        }
    } else {
        $usernameError = "Incorrect username or password";
        $passwordError = "Incorrect username or password";
        error_log("No user found with the username: $username");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Buddy Finder - Login</title>
    <link rel="stylesheet" href="log-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Travel Buddy Finder</h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="user">Username</label>
            <input type="text" name="Username" placeholder="Enter your username" id="uname" required>
            <?php if (!empty($usernameError)) { echo '<p class="error">'.$usernameError.'</p>'; } ?>

            <label for="pass">Password</label>
            <input type="password" name="Password" placeholder="Enter your password" id="pass" required>
            <?php if (!empty($passwordError)) { echo '<p class="error">'.$passwordError.'</p>'; } ?>

            <button type="submit">Login</button>
        </form>

        <div class="separator"><p>OR</p></div>

        <a href="#" class="google-btn">
            <img src="google.jpg" alt="Google logo" width="20">
            Sign in with Google
        </a>

        <div class="register-section">
            <p>Not registered yet? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
