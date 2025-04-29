<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    header("Location: ../admin/login.php");
    exit();
}

require('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    // Validate status
    $allowed_statuses = ['pending', 'completed', 'delivered'];
    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid status";
        header("Location: orders.php");
        exit();
    }
    
    // Update order status
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['success'] = "Order status updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update order status";
    }
    
    header("Location: orders.php");
    exit();
}
?>