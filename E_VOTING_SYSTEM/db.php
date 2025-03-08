<?php
$host = "localhost";
$dbname = "university_portal";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
