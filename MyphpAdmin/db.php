<?php
// Database connection settings for MySQL using mysqli
$servername = "localhost";  // Your MySQL server (usually 'localhost' if hosted locally)
$username = "root";  // Your MySQL username (the one you use to log in to phpMyAdmin)
$password = "";  // Your MySQL password (the one you set up for your MySQL account)
$dbname = "time_monitoring";  // Your database name (the one you want to connect to)

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
