<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config.php'; // Ensure this contains your MySQLi connection ($conn)

// Debugging: Log session and POST data
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting");
    header("Location: ../auth/login.php");
    exit();
}

// ===== NEW: CHECK IF THIS IS A PAYMENT REQUEST ===== //
if (isset($_POST['checkout'])) {
    // Create order in database
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'pending')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    
    // Move cart items to order_items
    $cart_items = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");
    while ($item = $cart_items->fetch_assoc()) {
        $insert = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $order_id, $item['food_id'], $item['quantity']);
        $insert->execute();
    }
    
    // Clear cart
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
    
    // Store order ID for payment processing
    $_SESSION['current_order_id'] = $order_id;
    
    // Redirect to Khalti payment
    header("Location: ../payment/khalti.php");
    exit();
}

// Process only POST requests with food_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
    $user_id = $_SESSION['user_id'];
    $food_id = intval($_POST['food_id']);
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

    error_log("Adding to cart - User: $user_id, Food: $food_id, Qty: $quantity");

    try {
        // Single query that handles both new items and quantity updates
        $sql = "INSERT INTO cart (user_id, food_id, quantity) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "iii", $user_id, $food_id, $quantity);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
        
        error_log("Cart updated successfully");
        $_SESSION['cart_message'] = "Item added to cart!";
        header("Location: /cms/users/cart.php");
        exit();

    } catch (Exception $e) {
        error_log("Error in cart operation: " . $e->getMessage());
        $_SESSION['error'] = "Failed to update cart. Please try again.";
        header("Location: ../users/menu.php");
        exit();
    }
} else {
    error_log("Invalid request method or missing food_id");
    header("Location: ../users/menu.php");
    exit();
}