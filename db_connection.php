<?php
$servername = "localhost: 3307"; 
$db_username = "root";     
$db_password = "";         
$dbname = "travel-bud";     

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
