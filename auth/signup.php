<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $role = 'customer'; 

    $checkQuery = "SELECT * FROM users WHERE email = :email";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([':email' => $email]);

    if ($checkStmt->rowCount() > 0) {
        echo "Email already exists. Try logging in or use a different one.";
        exit();
    }

   
    $query = "INSERT INTO users (username, email, phone_no, address, password, role)
              VALUES (:username, :email, :phone_no, :address, :password, :role)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':phone_no' => $phone_no,
        ':address' => $address,
        ':password' => $password,
        ':role' => $role
    ]);

    header("Location: login.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Signup Form Container */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f8fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 10vh;
    margin: 0;
    padding: 10px;
}

/* Form Styling */
form {
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 10px;
    width: 100%;
    max-width: 500px;
    margin: 10px 0;
}

h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
}

/* Form Elements Styling */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #34495e;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 19px;
    margin-bottom: 5px;
    transition: border-color 0.3s;
}

textarea {
    min-height: 3px;
    resize: vertical;
}

input:focus, textarea:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

/* Error Message Styling */
span[style="color: red;"] {
    display: block;
    font-size: 14px;
    margin-bottom: 15px;
    height: 18px;
}

/* Button Styling */
button[type="submit"] {
    width: 100%;
    padding: 14px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background-color: #2980b9;
}

/* Login Link Styling */
p {
    text-align: center;
    color: #7f8c8d;
    margin-top: 20px;
}

p a {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
}

p a:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 600px) {
    form {
        padding: 20px;
    }
    
    h1 {
        font-size: 24px;
    }
    
    input, textarea, button {
        padding: 10px;
    }
}
</style>

</head>
<h1>Sign Up</h1>
<form action="signup.php" method="POST" onsubmit="return validateForm()">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <span id="usernameError" style="color: red;"></span>
    <br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <span id="emailError" style="color: red;"></span>
    <br>

    <label for="phone_no">Phone Number:</label>
    <input type="text" name="phone_no" id="phone_no" required>
    <span id="phoneError" style="color: red;"></span>
    <br>

    <label for="address">Address:</label>
    <textarea name="address" id="address" required></textarea>
    <span id="addressError" style="color: red;"></span>
    <br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <span id="passwordError" style="color: red;"></span>
    <br>

    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a>.</p>
<script src="/cms/assets/script.js"></script>

</html>