<?php
session_start();
require '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    try {
        // Verify the cart item belongs to the user
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        $cartItem = $stmt->fetch();

        if (!$cartItem) {
            $_SESSION['error'] = "Cart item not found";
            header("Location: cart.php");
            exit();
        }

        $newQuantity = $cartItem['quantity'];

        if ($action === 'increase' && $newQuantity < 10) {
            $newQuantity++;
        } elseif ($action === 'decrease' && $newQuantity > 1) {
            $newQuantity--;
        }

        // Update quantity
        $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $updateStmt->execute([$newQuantity, $cart_id]);

        $_SESSION['success'] = "Cart updated successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating cart: " . $e->getMessage();
    }

    header("Location: ../users/cart.php");
    exit();
}