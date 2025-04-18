<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config.php'; // Ensure this contains your MySQLi connection ($conn)

// Debugging logs
error_log("==== CART REMOVAL REQUEST ==== " . date('Y-m-d H:i:s'));
error_log("Session data: " . print_r($_SESSION, true));
error_log("GET data: " . print_r($_GET, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in, redirecting");
    header("Location: ../users/login.php");
    exit();
}

// Process only if cart item ID is provided
if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    error_log("Attempting to remove cart item - User:$user_id, CartID:$cart_id");

    try {
        // Prepare the delete statement
        $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
        
        // Check if any rows were affected
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['success'] = "Item removed from cart.";
            error_log("Successfully removed cart item ID: $cart_id");
        } else {
            $_SESSION['error'] = "Item not found or doesn't belong to you.";
            error_log("No cart item found or unauthorized access");
        }
        
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        error_log("Error removing cart item: " . $e->getMessage());
        $_SESSION['error'] = "Failed to remove item: " . $e->getMessage();
    }
} else {
    error_log("Invalid request - no ID provided");
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to cart page
header("Location: ../../cms/users/cart.php");
exit();
?>