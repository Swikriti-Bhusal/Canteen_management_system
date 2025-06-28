<?php
session_start();
require_once '../config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Fetch cart items
$cart_sql = "SELECT c.food_id, c.quantity, f.price 
             FROM cart c
             JOIN food_items f ON c.food_id = f.id
             WHERE c.user_id = ?";
$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, "i", $user_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);

if (mysqli_num_rows($cart_result) === 0) {
    die("Your cart is empty.");
}

$cart_items = [];
$total_amount = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {
    $cart_items[] = $item;
    $total_amount += $item['price'] * $item['quantity'];
}
mysqli_stmt_close($cart_stmt);

// 2. Start database transaction
mysqli_begin_transaction($conn);

try {
    // 3. Insert order into 'orders' table
    $order_reference = uniqid('ORD-');
    $order_sql = "INSERT INTO orders 
        (user_id, order_reference, total_amount, status, payment_method, payment_status, created_at)
        VALUES (?, ?, ?, 'pending', 'cod', 'unpaid', NOW())";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "isd", $user_id, $order_reference, $total_amount);
    mysqli_stmt_execute($order_stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($order_stmt);

    // 4. Insert each cart item into 'order_items'
    $item_sql = "INSERT INTO order_items (order_id, food_id, quantity, price)
                 VALUES (?, ?, ?, ?)";
    $item_stmt = mysqli_prepare($conn, $item_sql);

    foreach ($cart_items as $item) {
        mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['food_id'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($item_stmt);
    }
    mysqli_stmt_close($item_stmt);

    // 5. Clear user's cart
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
    mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($clear_cart_stmt);
    mysqli_stmt_close($clear_cart_stmt);

    // 6. Commit transaction
    mysqli_commit($conn);

    // 7. Redirect to order confirmation
    header("Location: ../users/order_confirmation.php?order_id=" . $order_id);
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Something went wrong while placing your order. Please try again later.");
}
?>