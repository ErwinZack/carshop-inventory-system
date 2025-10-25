<?php
$servername = "localhost";
$username = "root";
$password = ""; // default XAMPP password
$dbname = "carshop_db updated";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>