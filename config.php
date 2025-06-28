<?php
// Database Configuration
$host = 'localhost';
$dbname = 'food_menu';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Sandbox Public & Secret Key 
define('KHALTI_PUBLIC_KEY', '9978ee3d685145dcb362b76d49521def'); 
define('KHALTI_SECRET_KEY', '326c863301614df782eadbb802d629a2'); 

// API Endpoints
define('KHALTI_VERIFY_URL', 'https://khalti.com/api/v2/payment/verify/');

// Redirect after success
define('KHALTI_RETURN_URL', 'http://localhost/cms/users/order_confirmation.php');

// Mode Toggle
define('KHALTI_TEST_MODE', true); // true = sandbox, false = live

// Test Payment Info (for reference/testing only)
define('KHALTI_TEST_MOBILE', '9800000001');
define('KHALTI_TEST_PIN', '1111');
//OTP = 987654
?>


