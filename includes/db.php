
<?php
// Database Configuration (for development)
$host = 'localhost';
$dbname = 'food_menu';
$username = 'root';
$password = '';


// Create connection with error handling
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed. Please check your config. Error: " . mysqli_connect_error());
}

// Set UTF-8 encoding
mysqli_set_charset($conn, 'utf8mb4');


?>