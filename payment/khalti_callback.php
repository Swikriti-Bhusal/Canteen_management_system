<?php
session_start();
require_once '../config.php';

// Check if pidx is provided
if (!isset($_GET['pidx'])) {
    die("Invalid redirect - PIDX missing.");
}

$pidx = $_GET['pidx'];

// Set up headers for Khalti API
$headers = [
    "Authorization: Key " . KHALTI_SECRET_KEY,
    "Content-Type: application/json"
];

$lookup_data = json_encode(['pidx' => $pidx]);

// Make API request to Khalti
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

// Check for cURL errors
if ($curl_error) {
    die("cURL error: " . $curl_error);
}

$result = json_decode($response, true);

// Log response for debugging
file_put_contents('khalti_callback_log.txt', print_r($result, true));

// Verify payment status
if (isset($result['status']) && $result['status'] === 'Completed') {
    // Extract data from response
    $total_amount = isset($result['total_amount']) ? $result['total_amount'] / 100 : null;
    $order_id = $pidx; // Use pidx as order_id
    $payment_id = isset($result['transaction_id']) ? $result['transaction_id'] : $pidx; // Use transaction_id or fallback to pidx

    if ($total_amount === null) {
        die("Error: Total amount not provided in Khalti response.");
    }

    // Get user and cart data
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $cart_items = isset($_SESSION['cart_items']) ? json_decode($_SESSION['cart_items'], true) : [];

    if (!$user_id || empty($cart_items)) {
        die("Error: User ID or cart items missing.");
    }

    // Generate order_reference
    $order_reference = 'ORD-' . time() . '-' . $user_id;

    // Prepare SQL query
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
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("issss", $user_id, $order_reference, $total_amount, $payment_id, $order_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    // Clear cart after successful order
    unset($_SESSION['cart_items']);

    // Display success message with Continue Shopping button
    echo "
        <h3>Payment Successful! Order ID: $order_id</h3>
        <p>Thank you for your purchase!</p>
        <a href='/cms/index.php' style='display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Continue Shopping</a>
    ";
} else {
    echo "<h3>Payment failed or cancelled.</h3>";
    print_r($result);
}

$conn->close();
?>