<?php
session_start();
require '../config.php'; // Make sure config.php is also in cms/

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    try {
        // Delete the item only if it belongs to the logged-in user
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Item removed from cart.";
        } else {
            $_SESSION['error'] = "Item not found or doesn't belong to you.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to remove item: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to cart page
header("Location: ../../cms/users/cart.php");
exit();
