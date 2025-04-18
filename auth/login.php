
<?php 
session_start();
include '../config.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 


    echo "Submitted Email: $email<br>";
    echo "Submitted Password: $password<br>";


    
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);  // prepare the SQL


    if ($stmt) {
        $stmt->bind_param("s", $email); // "s" = string type
        $stmt->execute();


        $result = $stmt->get_result(); // get result from query
        $user = $result->fetch_assoc(); // fetch as associative array


        echo "User Data from Database:<br>";
        print_r($user);


       
        if ($user && $password === $user['password']) { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];


            
            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../users/menu.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }


        $stmt->close(); 
    } else {
        $error = "Database error: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
    
</head>
<body>
        <h1>Login</h1>


    <main>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form id="login-form" action="login.php" method="POST">
            <div class="form-control">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <small></small>
            </div>
            <div class="form-control">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <small></small>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </main>


    
    <script src="../assets/script.js"></script>
</body>
</html>
