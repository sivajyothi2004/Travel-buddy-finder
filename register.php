<?php
// Include the database connection
require 'db_connection.php';

$errors = [];
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form inputs
    $fullname = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $profile_pic = $_FILES['profile-pic'];
    $govt_id = $_FILES['govt-id'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Validate password
    if ($password !== $confirm_password) {
        $errors['password'] = "Passwords do not match";
    } elseif (!preg_match("/^(?=.*[!@#$%^&*])(?=.*\d).{8,}$/", $password)) {
        $errors['password'] = "Password must be at least 8 characters, with 1 special character and 1 number";
    }

    // Validate phone number (India format)
    if (!preg_match("/^[6-9]\d{9}$/", $phone)) {
        $errors['phone'] = "Phone number must be valid for India (start with 6-9 and be 10 digits long)";
    }

    // Check if username already exists
    $stmt_check_username = $conn->prepare("SELECT username FROM register WHERE username = ?");
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $stmt_check_username->store_result();
    if ($stmt_check_username->num_rows > 0) {
        $errors['username'] = "Username already exists. Please choose a different one.";
    }
    $stmt_check_username->close();

    // Validate file sizes
    if ($profile_pic['size'] > 2097152) {
        $errors['profile-pic'] = "Profile picture must not exceed 2MB.";
    }
    if ($govt_id['size'] > 2097152) {
        $errors['govt-id'] = "Government ID must not exceed 2MB.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle file uploads
        $profile_pic_data = file_get_contents($profile_pic['tmp_name']);
        $govt_id_data = file_get_contents($govt_id['tmp_name']);

        // Insert data into the register table
        $stmt = $conn->prepare("INSERT INTO register (fullname, email, username, password, id, phone, country, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssbis", $fullname, $email, $username, $hashed_password, $govt_id_data, $phone, $country, $profile_pic_data);

        if ($stmt->execute()) {
            // Insert data into the extra table
            $stmt_extra = $conn->prepare("INSERT INTO extra (username) VALUES (?)");
            $stmt_extra->bind_param("s", $username);
            
            if ($stmt_extra->execute()) {
                $success_message = "Registration successful! You can now log in.";
            } else {
                $errors['extra_insert'] = "Error inserting into extra table: " . $stmt_extra->error;
            }
            $stmt_extra->close();
        } else {
            $errors['register_insert'] = "Error registering user: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travel Buddy Finder - Register</title>
  <link rel="stylesheet" href="reg-styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

  <div class="register-container">
    <h1>Create Your Account</h1>

    <?php if (!empty($success_message)): ?>
      <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST" enctype="multipart/form-data">
      
      <label for="name">Full Name <span class="required">*</span></label>
      <input type="text" id="name" name="name" placeholder="Enter your full name" required>
      <?php if (isset($errors['name'])) echo "<p style='color:red;'>{$errors['name']}</p>"; ?>

      <label for="email">Email ID <span class="required">*</span></label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>
      <?php if (isset($errors['email'])) echo "<p style='color:red;'>{$errors['email']}</p>"; ?>

      <label for="profile-pic">Upload Profile Picture <span class="required">*</span></label>
      <input type="file" id="profile-pic" name="profile-pic" accept="image/*" required>
      <?php if (isset($errors['profile-pic'])) echo "<p style='color:red;'>{$errors['profile-pic']}</p>"; ?>

      <label for="username">Preferred Username <span class="required">*</span></label>
      <input type="text" id="username" name="username" placeholder="Enter your preferred username" required>
      <?php if (isset($errors['username'])) echo "<p style='color:red;'>{$errors['username']}</p>"; ?>

      <label for="password">Password <span class="required">*</span></label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>
      <?php if (isset($errors['password'])) echo "<p style='color:red;'>{$errors['password']}</p>"; ?>

      <label for="confirm-password">Confirm Password <span class="required">*</span></label>
      <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>

      <label for="govt-id">Upload Government Verified ID <span class="required">*</span></label>
      <input type="file" id="govt-id" name="govt-id" accept=".jpg, .jpeg, .png, .pdf" required>
      <?php if (isset($errors['govt-id'])) echo "<p style='color:red;'>{$errors['govt-id']}</p>"; ?>

      <label for="phone">Phone Number <span class="required">*</span></label>
      <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
      <?php if (isset($errors['phone'])) echo "<p style='color:red;'>{$errors['phone']}</p>"; ?>

      <label for="country">Country <span class="required">*</span></label>
      <input type="text" id="country" name="country" placeholder="Enter your country" required>

      <button type="submit">Register</button>
    </form>

    <div class="separator"><p>OR</p></div>

    <a href="login.php" class="login-link">Already have an account? Login here</a>
  </div>

</body>
</html>
