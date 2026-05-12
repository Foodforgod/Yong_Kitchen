<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "restaurant_db"; 
$port = 3306; 

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Database Connection Error: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");