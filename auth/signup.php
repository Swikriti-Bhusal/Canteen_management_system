<?php
session_start();
require '../config.php'; // existing MySQLi connection file

$errors = [];
$username = $email = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
if (empty($username)) {
    $errors['username'] = "Username is required";
} elseif (strlen($username) < 4) {
    $errors['username'] = "Username must be at least 4 characters";
} elseif (!preg_match('/^[A-Za-z]+$/', $username)) {
    $errors['username'] = "Username can only contain letters (no numbers/symbols)";
}

if (empty($email)) {
    $errors['email'] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format";
} elseif (preg_match('/^\d+@/', $email)) {
    $errors['email'] = "Email cannot start with numbers (e.g., 1@gmail.com)";
}

if (empty($phone)) {
    $errors['phone'] = "Phone number is required";
} elseif (!preg_match('/^\d{10}$/', $phone)) {
    $errors['phone'] = "Phone must be 10 digits";
} elseif (preg_match('/^(\d)\1{9}$/', $phone)) {
    $errors['phone'] = "Phone cannot be all repeating digits";
}

if (empty($address)) {
    $errors['address'] = "Address is required";
} elseif (!preg_match('/[A-Za-z]/', $address)) {
    $errors['address'] = "Address must contain at least 1 letter";
}

if (empty($password)) {
    $errors['password'] = "Password is required";
} elseif (strlen($password) < 4) {
    $errors['password'] = "Password must be at least 4 characters";
} elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
    $errors['password'] = "Password must contain at least 1 letter and 1 number";
}
  
    
    // Check if username/email already exists
    if (empty($errors)) {
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors['general'] = "Username or email already exists";
        }
        $stmt->close();
    }
    
    // If no errors, create user
    if (empty($errors)) {
        $level = 2;  // Default level
        
        $insert_query = "INSERT INTO users (username, email, phone, address, password, level) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssssss", $username, $email, $phone, $address, $password, $level);
        
        if ($stmt->execute()) {
            $_SESSION['signup_success'] = "Registration successful! ";
            header("Location: ../users/menu.php");
            exit();
        } else {
            $errors['general'] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System - Sign Up</title>
    <link rel="stylesheet" href="../cms/assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-row {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Sign Up</h1>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="error"><?php echo $errors['general']; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-row">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" 
                   value="<?php echo htmlspecialchars($username); ?>" required>
            <?php if (!empty($errors['username'])): ?>
                <div class="error"><?php echo $errors['username']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" 
                   value="<?php echo htmlspecialchars($email); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <label for="phone">Phone:</label>
            <input type="tel" name="phone" id="phone" 
                   value="<?php echo htmlspecialchars($phone); ?>" required>
            <?php if (!empty($errors['phone'])): ?>
                <div class="error"><?php echo $errors['phone']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <label for="address">Address:</label>
            <textarea name="address" id="address" required><?php echo htmlspecialchars($address); ?></textarea>
            <?php if (!empty($errors['address'])): ?>
                <div class="error"><?php echo $errors['address']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

    <script>
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            
            // Validate username
            const username = document.getElementById('username').value.trim();
            if (username.length < 4) {
                document.getElementById('usernameError').textContent = 'Username must be at least 4 characters';
                valid = false;
            } else {
                document.getElementById('usernameError').textContent = '';
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>