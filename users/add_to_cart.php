<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'food_menu';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Debug: Check session
error_log("Session data: " . print_r($_SESSION, true));

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting");
    header("Location: ../auth/login.php");
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
    $user_id = $_SESSION['user_id'];
    $food_id = intval($_POST['food_id']);
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

    error_log("Adding to cart - User: $user_id, Food: $food_id, Qty: $quantity");

    try {
        // Check if item already exists in cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND food_id = ?");
        $stmt->execute([$user_id, $food_id]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $updateStmt->execute([$newQuantity, $existingItem['id']]);
            error_log("Updated existing cart item");
        } else {
            // Insert new item
            $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, food_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->execute([$user_id, $food_id, $quantity]);
            error_log("Added new cart item");
        }

        // Redirect back to menu with success message
        $_SESSION['cart_message'] = "Item added to cart!";
        header("Location: /cms/users/cart.php");
        exit();

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("Database error: " . $e->getMessage());
    }
} else {
    // Invalid request
    error_log("Invalid request method or missing food_id");
    header("Location: ../users/menu.php");
    exit();
}
?>