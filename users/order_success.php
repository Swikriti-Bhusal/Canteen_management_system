<?php
session_start();
require_once '../config.php';
require_once '../payment/khalti_integration.php';

// Redirect if not a valid callback
if (!isset($_GET['pidx']) || !isset($_GET['order_reference']) || !isset($_SESSION['khalti_pidx']) || !isset($_SESSION['pending_order_reference'])) {
    error_log("Invalid callback: pidx or order_reference missing");
    header("Location: checkout.php");
    exit;
}

$khalti = new KhaltiIntegration(
    secret_key: KHALTI_SECRET_KEY,
    is_sandbox: KHALTI_TEST_MODE
);

// Verify payment
$verification = $khalti->verifyPayment($_GET['pidx']);
error_log("Khalti Payment Verification Response: " . json_encode($verification));

if ($verification['success'] && $verification['status'] === 'Completed') {
    $order_reference = $_SESSION['pending_order_reference'];
    $user_id = $_SESSION['user_id'];
    $amount = $verification['amount']; // No VAT, uses verified amount
    $transaction_id = $verification['transaction_id'];
    $cart_items = $_SESSION['pending_cart_items'] ?? [];

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert order
        $order_sql = "INSERT INTO orders (user_id, order_reference, total_amount, status, payment_status, payment_method, payment_id, payment_date, created_at)
                      VALUES (?, ?, ?, 'pending', 'paid', 'khalti', ?, NOW(), NOW())";
        $order_stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($order_stmt, "isds", $user_id, $order_reference, $amount, $transaction_id);
        mysqli_stmt_execute($order_stmt);
        mysqli_stmt_close($order_stmt);

        // Insert order items
        foreach ($cart_items as $item) {
            $item_sql = "INSERT INTO order_items (order_reference, food_id, quantity, price)
                         VALUES (?, ?, ?, ?)";
            $item_stmt = mysqli_prepare($conn, $item_sql);
            mysqli_stmt_bind_param($item_stmt, "siid", $order_reference, $item['food_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($item_stmt);
            mysqli_stmt_close($item_stmt);
        }

        // Clear cart
        $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
        mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
        mysqli_stmt_execute($clear_cart_stmt);
        mysqli_stmt_close($clear_cart_stmt);

        // Commit transaction
        mysqli_commit($conn);

        // Clear session variables
        unset($_SESSION['khalti_pidx']);
        unset($_SESSION['pending_order_reference']);
        unset($_SESSION['pending_cart_items']);

        $message = "Payment successful! Order Reference: " . htmlspecialchars($order_reference);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Database error in order_success.php: " . $e->getMessage());
        $message = "Payment processing failed: " . $e->getMessage();
        header("Location: checkout.php?error=" . urlencode($message));
        exit;
    }
} else {
    $message = "Payment verification failed: " . ($verification['error'] ?? 'Unknown error');
    error_log("Payment verification failed: " . ($verification['error'] ?? 'Unknown error'));
    header("Location: checkout.php?error=" . urlencode($message));
    exit;
}
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
            text-align: center;
        }
        .success {
            color: green;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .btn {
            color: white;
            background: #5C2D91;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn:hover {
            background: #4a2473;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <div class="success"><?= $message ?></div>
        <a href="checkout.php" class="btn">Back to Checkout</a>
    </div>
</body>
</html>