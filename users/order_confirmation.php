
<?php
session_start();
require_once '../config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_reference = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$order_id = intval($order_reference);

$order_sql = "SELECT *, 
              CASE 
                WHEN payment_method = 'khalti' THEN 'Paid via Khalti'
                WHEN payment_method = 'cod' THEN 'Cash on Delivery'
                ELSE 'Payment Pending'
              END as payment_status_text
              FROM orders WHERE id = ? AND user_id = ?";

$order_stmt = mysqli_prepare($conn, $order_sql);
if (!$order_stmt) {
    die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
}

mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);
mysqli_stmt_close($order_stmt);

if (!$order) {
    die("Order not found or doesn't belong to you");
}

// Fetch order items
$items_sql = "SELECT oi.*, fi.name 
              FROM order_items oi
              JOIN food_items fi ON oi.food_id = fi.id
              WHERE oi.order_id = ?";
$items_stmt = mysqli_prepare($conn, $items_sql);
$order_id = $order['id'];
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);
$order_items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
mysqli_stmt_close($items_stmt);

// Determine status color
$status_color = '#4CAF50'; // Green
if ($order['status'] == 'pending') $status_color = '#FFC107'; // Yellow
if ($order['status'] == 'cancelled') $status_color = '#F44336'; // Red
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
        .confirmation-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .confirmation-icon {
            font-size: 50px;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        .order-details {
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
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .order-number {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .order-status {
            padding: 5px 10px;
            background: <?= $status_color ?>;
            color: white;
            border-radius: 4px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 15px;
        }
        .payment-method {
            margin: 15px 0;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 4px;
        }
        .khalti-logo {
            height: 20px;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-header">
            <div class="confirmation-icon">✓</div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your order</p>
        </div>

        <div class="order-details">
            <div class="order-number">Order #<?= $order['id'] ?></div>
            <div class="order-status"><?= ucfirst($order['status']) ?></div>
            
            <div class="payment-method">
                <strong>Payment Method:</strong> 
                <?= $order['payment_status_text'] ?>
                <?php if ($order['payment_method'] == 'khalti'): ?>
                    <img src="https://khalti.com/static/khalti-logo.svg" alt="Khalti" class="khalti-logo">
                <?php endif; ?>
            </div>
            
            <p>Order placed on <?= date('F j, Y \a\t g:i a', strtotime($order['created_at'])) ?></p>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                    <span>Rs <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                Total: Rs <?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>

        <a href="menu.php" class="btn">Continue Shopping</a>
        
        <?php if ($order['payment_method'] == 'khalti'): ?>
            <div style="margin-top: 20px; font-size: 0.9rem; color: #666;">
                <p>Your Khalti payment was successful. A receipt has been sent to your registered Khalti account.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>