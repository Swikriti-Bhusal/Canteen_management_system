<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config.php'; // Ensure this contains your MySQLi connection ($conn)

// Debugging logs
error_log("==== CART UPDATE REQUEST ==== " . date('Y-m-d H:i:s'));
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting");
    header("Location: ../users/login.php");
    exit();
}

// Process only POST requests with required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['action'])) {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    error_log("Updating cart - User:$user_id, CartID:$cart_id, Action:$action");

    // Verify the cart item belongs to the user
    $verify_sql = "SELECT * FROM cart WHERE id = ? AND user_id = ?";
    $verify_stmt = mysqli_prepare($conn, $verify_sql);

    if (!$verify_stmt) {
        error_log("Verify prepare failed: " . mysqli_error($conn));
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: ../users/cart.php");
        exit();
    }

    mysqli_stmt_bind_param($verify_stmt, "ii", $cart_id, $user_id);
    mysqli_stmt_execute($verify_stmt);
    $result = mysqli_stmt_get_result($verify_stmt);
    $cartItem = mysqli_fetch_assoc($result);
    mysqli_stmt_close($verify_stmt);

    if (!$cartItem) {
        error_log("Invalid cart item or unauthorized access");
        $_SESSION['error'] = "Cart item not found";
        header("Location: ../users/cart.php");
        exit();
    }

    // Calculate new quantity (with limits 1-50)
    $newQuantity = $cartItem['quantity'];
    
    if ($action === 'increase' && $newQuantity < 50) {
        $newQuantity++;
    } elseif ($action === 'decrease' && $newQuantity > 1) {
        $newQuantity--;
    } else {
        error_log("Invalid action or quantity limit reached");
        $_SESSION['error'] = "Invalid action or quantity limit reached";
        header("Location: ../users/cart.php");
        exit();
    }

    // Update quantity in database
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);

    if (!$update_stmt) {
        error_log("Update prepare failed: " . mysqli_error($conn));
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: ../users/cart.php");
        exit();
    }

    mysqli_stmt_bind_param($update_stmt, "ii", $newQuantity, $cart_id);
    
    if (!mysqli_stmt_execute($update_stmt)) {
        error_log("Update execute failed: " . mysqli_stmt_error($update_stmt));
        $_SESSION['error'] = "Failed to update cart";
    } else {
        $_SESSION['success'] = "Cart updated successfully";
        error_log("Cart updated successfully - New Qty: $newQuantity");
    }

    mysqli_stmt_close($update_stmt);
    header("Location: ../users/cart.php");
    exit();
} else {
    error_log("Invalid request method or missing parameters");
    $_SESSION['error'] = "Invalid request";
    header("Location: ../users/menu.php");
    exit();
}
?>