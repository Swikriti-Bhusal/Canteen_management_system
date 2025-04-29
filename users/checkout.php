<?php
session_start();
require_once '../config.php'; // Contains MySQLi connection ($conn)

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch cart items
$cart_sql = "SELECT c.*, f.name, f.price 
             FROM cart c
             JOIN food_items f ON c.food_id = f.id
             WHERE c.user_id = ?";
$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);
$cartItems = mysqli_fetch_all($cart_result, MYSQLI_ASSOC);
mysqli_stmt_close($cart_stmt);

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle checkout
// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order record
    $order_sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "id", $_SESSION['user_id'], $total);
    mysqli_stmt_execute($order_stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($order_stmt);
    
    // Save each cart item to order_items
    foreach ($cartItems as $item) {
        $item_sql = "INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)";
        $item_stmt = mysqli_prepare($conn, $item_sql);
        mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['food_id'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($item_stmt);
        mysqli_stmt_close($item_stmt);
    }
    
    // Clear cart
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
    mysqli_stmt_bind_param($clear_cart_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($clear_cart_stmt);
    mysqli_stmt_close($clear_cart_stmt);
    
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .checkout-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 10px;
            text-align: right;
        }
        .btn {
            background: #ff6b00;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="checkout-header">
            <h1>Confirm Your Order</h1>
        </div>

        <form action="checkout.php" method="post">
            <div class="order-summary">
                <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                        <span>Rs <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="order-total">
                    Total: Rs <?= number_format($total, 2) ?>
                </div>
            </div>

            <button type="submit" class="btn">Confirm Order</button>
        </form>
    </div>
</body>
</html>