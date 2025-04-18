<?php
session_start();
require_once '../config.php'; // Contains MySQLi connection ($conn)

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables with default values
$user = ['name' => '', 'email' => '', 'phone' => ''];
$cartItems = [];
$total = 0;

// Fetch user details with error handling
$user_sql = "SELECT * FROM users WHERE id = ?";
if ($user_stmt = mysqli_prepare($conn, $user_sql)) {
    mysqli_stmt_bind_param($user_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    if ($user_data = mysqli_fetch_assoc($user_result)) {
        $user = $user_data;
    }
    mysqli_stmt_close($user_stmt);
}

// Fetch cart items
$cart_sql = "SELECT c.*, f.name, f.price 
             FROM cart c
             JOIN food_items f ON c.food_id = f.id
             WHERE c.user_id = ?";
if ($cart_stmt = mysqli_prepare($conn, $cart_sql)) {
    mysqli_stmt_bind_param($cart_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($cart_stmt);
    $cart_result = mysqli_stmt_get_result($cart_stmt);
    $cartItems = mysqli_fetch_all($cart_result, MYSQLI_ASSOC);
    mysqli_stmt_close($cart_stmt);
}

// Calculate total
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $delivery_address = $_POST['delivery_address'] ?? '';
    $special_instructions = $_POST['special_instructions'] ?? '';
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Create order
        $order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, delivery_address, special_instructions, status)
                      VALUES (?, ?, ?, ?, ?, 'pending')";
        $order_stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($order_stmt, "idsss", $_SESSION['user_id'], $total, $payment_method, $delivery_address, $special_instructions);
        mysqli_stmt_execute($order_stmt);
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($order_stmt);
        
        // Add order items
        $order_item_sql = "INSERT INTO order_items (order_id, food_id, quantity, price)
                           VALUES (?, ?, ?, ?)";
        $order_item_stmt = mysqli_prepare($conn, $order_item_sql);
        
        foreach ($cartItems as $item) {
            mysqli_stmt_bind_param($order_item_stmt, "iiid", $order_id, $item['food_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($order_item_stmt);
        }
        mysqli_stmt_close($order_item_stmt);
        
        // Clear cart
        $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
        mysqli_stmt_bind_param($clear_cart_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($clear_cart_stmt);
        mysqli_stmt_close($clear_cart_stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $error = "Checkout failed: " . $e->getMessage();
    }
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
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .checkout-section {
            margin-bottom: 20px;
        }
        .checkout-title {
            border-bottom: 2px solid #ff6b00;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .order-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
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
            margin-top: 20px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your order details</p>
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </div>

        <form action="checkout.php" method="post">
            <div class="checkout-grid">
                <div class="checkout-left">
                    <div class="checkout-section">
                        <h3 class="checkout-title">Delivery Information</h3>
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_address">Delivery Address</label>
                            <textarea id="delivery_address" name="delivery_address" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions</label>
                            <textarea id="special_instructions" name="special_instructions"></textarea>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3 class="checkout-title">Payment Method</h3>
                        <div class="form-group">
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="cash">Cash on Delivery</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="khalti">Khalti</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="checkout-right">
                    <div class="checkout-section">
                        <h3 class="checkout-title">Order Summary</h3>
                        <div class="order-summary">
                            <?php if (empty($cartItems)): ?>
                                <p>Your cart is empty</p>
                            <?php else: ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="order-item">
                                        <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                        <span>Rs <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="order-total">
                                    Total: Rs <?= number_format($total, 2) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($cartItems)): ?>
                <button type="submit" class="btn">Place Order</button>
            <?php else: ?>
                <a href="menu.php" class="btn" style="background: #6c757d;">Continue Shopping</a>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>