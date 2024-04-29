<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_madrid";

$BASE_URL = 'http://localhost/FootballClubManagement/api.php';


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
