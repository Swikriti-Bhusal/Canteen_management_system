<?php 
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 
    
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
       
        if ($user && $password === $user['password']) { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = (int)$user['level']; // Force integer type
            
           
            if ((int)$user['level'] === 1) {
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
</html