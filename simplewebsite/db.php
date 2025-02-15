<?php
$servername = "localhost"; // Typically 'localhost'
$username = "root";        // Your database username (default: root)
$password = "";            // Your database password (default: none)
$dbname = "mydatabase";    // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
