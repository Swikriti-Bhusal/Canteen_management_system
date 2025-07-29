<?php
session_start();
require_once '../config.php';

//  1. Check if pidx is present
if (!isset($_GET['pidx']) || empty($_GET['pidx'])) {
    die(" Invalid redirect: PIDX missing.");
}

$pidx = $_GET['pidx'];

//  2. Prepare headers for API lookup
$headers = [
    "Authorization: Key " . KHALTI_SECRET_KEY,
    "Content-Type: application/json"
];

$lookup_data = json_encode(['pidx' => $pidx]);

// 3. Make lookup request to Khalti
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/lookup/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $lookup_data,
    CURLOPT_HTTPHEADER => $headers
]);

$response = curl_exec($curl);
$curl_error = curl_error($curl);
curl_close($curl);

//  4. Handle cURL errors
if ($curl_error) {
    die(" cURL Error: $curl_error");
}

$result = json_decode($response, true);

//  5. Debug log
file_put_contents('khalti_callback_log.txt', print_r($result, true));

//  6. Check if payment was completed
if (isset($result['status']) && $result['status'] === 'Completed') {
    $total_amount = isset($result['total_amount']) ? $result['total_amount'] / 100 : 0;
    $payment_id = $result['transaction_id'] ?? $pidx;
    $order_id = $pidx;

    //  7. Fetch user and cart session data
    $user_id = $_SESSION['user_id'] ?? null;
    $cart_items = isset($_SESSION['cart_items']) ? $_SESSION['cart_items'] : [];

    // $cart_items = isset($_SESSION['cart_items']) ? json_decode($_SESSION['cart_items'], true) : [];

    if (!$user_id || empty($cart_items)) {
        die(" Error: Missing user ID or cart.");
    }

    //  8. Generate order_reference
    $order_reference = 'ORD-' . time() . '-' . $user_id;

    //  9. Insert into orders table
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, 
            order_reference, 
            total_amount, 
            payment_status, 
            payment_method, 
            payment_id, 
            payment_date, 
            order_id
        ) VALUES (?, ?, ?, 'paid', 'khalti', ?, NOW(), ?)
    ");

    if (!$stmt) {
        die(" DB Error (Prepare): " . $conn->error);
    }

    $stmt->bind_param("issss", $user_id, $order_reference, $total_amount, $payment_id, $order_id);

    if (!$stmt->execute()) {
        die(" DB Error (Execute): " . $stmt->error);
    }

    $stmt->close();

    //  10. Clear cart
    unset($_SESSION['cart_items']);

    //  11. Success message
    echo "
        <div style='font-family: Arial; padding: 40px; text-align: center;'>
            <h2 style='color: #28a745;'>🎉 Payment Successful!</h2>
            <p>Order Reference: <strong>$order_reference</strong></p>
            <p>Transaction ID: <strong>$payment_id</strong></p>
            <a href='/cms/users/menu.php' style='
                display: inline-block; 
                padding: 12px 24px; 
                background-color: #007bff; 
                color: #fff; 
                border-radius: 5px; 
                text-decoration: none;
                margin-top: 20px;
            '>Continue Shopping</a>
        </div>
    ";
} else {
    // Payment failed or not completed
    echo "<h3 style='color: red;'> Payment failed or was cancelled.</h3>";
    echo "<pre>" . print_r($result, true) . "</pre>";
}

$conn->close();
?>
