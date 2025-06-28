<?php
require_once '../config.php';

// Get the raw POST data
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

// Get the signature from headers
$receivedSignature = $_SERVER['HTTP_X_KHALTI_SIGNATURE'] ?? '';

// Validate signature
$data = [
    $payload['pidx'],
    $payload['amount'],
    $payload['mobile'] ?? '',
    $payload['purchase_order_id'],
    $payload['purchase_order_name'],
    KHALTI_SECRET_KEY
];
$signatureString = implode('.', $data);
$generatedSignature = hash('sha256', $signatureString);

if (!hash_equals($generatedSignature, $receivedSignature)) {
    http_response_code(401);
    die('Invalid signature');
}

try {
    // Process based on payment status
    if ($payload['status'] === 'Completed') {
        // Update payment status
        $sql = "UPDATE payments SET status = 'completed', transaction_id = ?, 
                updated_at = NOW() WHERE pidx = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $payload['transaction_id'], $payload['pidx']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Create order if not exists
        $orderCheck = mysqli_query($conn, 
            "SELECT id FROM orders WHERE order_id = '{$payload['purchase_order_id']}'");
        if (mysqli_num_rows($orderCheck) == 0) {
            // Get payment details
            $payment = mysqli_query($conn, 
                "SELECT * FROM payments WHERE pidx = '{$payload['pidx']}'");
            $paymentData = mysqli_fetch_assoc($payment);

            // Create order
            $orderSql = "INSERT INTO orders 
                        (order_id, user_id, total_amount, payment_method, 
                         payment_status, created_at)
                         VALUES (?, ?, ?, 'khalti', 'completed', NOW())";
            $stmt = mysqli_prepare($conn, $orderSql);
            mysqli_stmt_bind_param($stmt, "sid", 
                $payload['purchase_order_id'],
                $paymentData['user_id'], // You'll need to store user_id in payments table
                $payload['amount'] / 100); // Convert back to rupees
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        http_response_code(200);
        echo 'OK';
    } else {
        // Handle other statuses
        http_response_code(200);
        echo 'OK (not completed)';
    }
} catch (Exception $e) {
    error_log("Webhook error: " . $e->getMessage());
    http_response_code(500);
    echo 'Error processing webhook';
}