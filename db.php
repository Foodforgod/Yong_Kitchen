<?php
$host = "localhost";
$user = "root";
$pass = ""; 
<<<<<<< HEAD
$dbname = "restaurant_db"; 
=======
$dbname = "restaurants_db"; 
>>>>>>> 92d048ecd14d6e8b2b7a0d0cf5103462c8ae04d6

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
