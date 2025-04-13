<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); // Remove extra spaces
    $password = trim($_POST['password']); // Remove extra spaces

    // Debugging: Print the submitted email and password
    echo "Submitted Email: $email<br>";
    echo "Submitted Password: $password<br>";

    // Fetch user from the database
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Print the user data fetched from the database
    echo "User Data from Database:<br>";
    print_r($user);

    if ($user && $password === $user['password']) { // Compare plain passwords
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../users/menu.php");
        }
        exit();
    } else {
        // Login failed
        $error = "Invalid email or password.";
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

    
    <script src="../assets/js/script.js"></script>
</body>
</html>