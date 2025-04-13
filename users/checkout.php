<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

// Fetch cart items
$cartStmt = $pdo->prepare("
    SELECT ci.*, fi.name, fi.price 
    FROM cart_items ci
    JOIN food_item fi ON ci.food_id = fi.id
    WHERE ci.user_id = ?
");
$cartStmt->execute([$_SESSION['user_id']]);
$cartItems = $cartStmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $delivery_address = $_POST['delivery_address'];
    $special_instructions = $_POST['special_instructions'] ?? '';
    
    // Create order
    $orderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, payment_method, delivery_address, special_instructions, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $orderStmt->execute([$_SESSION['user_id'], $total, $payment_method, $delivery_address, $special_instructions]);
    $order_id = $pdo->lastInsertId();
    
    // Add order items
    foreach ($cartItems as $item) {
        $orderItemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, food_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $orderItemStmt->execute([$order_id, $item['food_id'], $item['quantity'], $item['price']]);
    }
    
    // Clear cart
    $clearCartStmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clearCartStmt->execute([$_SESSION['user_id']]);
    
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
        </div>

        <form action="checkout.php" method="post">
            <div class="checkout-grid">
                <div class="checkout-left">
                    <div class="checkout-section">
                        <h3 class="checkout-title">Delivery Information</h3>
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                        
                    </div>

                    <div class="checkout-section">
                        <h3 class="checkout-title">Payment Method</h3>
                        <div class="form-group">
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="cash">Cash on Delivery</option>
                                <option value="card">Khalti</option>
                                
                            </select>
                        </div>
                    </div>
                </div>

                <div class="checkout-right">
                    <div class="checkout-section">
                        <h3 class="checkout-title">Order Summary</h3>
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
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>
</body>
</html>