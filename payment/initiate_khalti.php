<?php
session_start();
require_once '../config.php';

// Set header for JSON response
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 1 only for development

// Get POSTed JSON input
$input = json_decode(file_get_contents('php://input'), true);

$amount     = $input['amount'] ?? null;
$order_id   = $input['order_id'] ?? null;
$user_id    = $input['user_id'] ?? null;
$cart_items = $input['cart_items'] ?? [];

if (!$amount || !$order_id || !$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields',
    ]);
    exit;
}

// Fetch user details from DB (fallback if not in session)
$name  = $_SESSION['user_name']  ?? '';
$email = $_SESSION['user_email'] ?? '';
$phone = $_SESSION['user_phone'] ?? '';

if (empty($name) || empty($email) || empty($phone)) {
    $stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user  = $res->fetch_assoc();
        $name  = $user['username'];
        $email = $user['email'];
        $phone = $user['phone'];
    }
    $stmt->close();
}

// Prepare payload for Khalti
$data = [
    'return_url'          => 'http://localhost/cms/payment/khalti_callback.php',
    'website_url'         => 'http://localhost/cms',
    'amount'              => (int) $amount,
    'purchase_order_id'   => $order_id,
    'purchase_order_name' => 'Canteen Order',
    'customer_info'       => [
        'name'  => $name,
        'email' => $email,
        'phone' => $phone
    ]
];

$headers = [
    "Authorization: Key " . KHALTI_SECRET_KEY,
    "Content-Type: application/json"
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL            => "https://a.khalti.com/api/v2/epayment/initiate/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HTTPHEADER     => $headers
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// Decode Khalti response
$result = json_decode($response, true);

if ($err) {
    echo json_encode([
        'success' => false,
        'message' => 'CURL Error: ' . $err
    ]);
    exit;
}

if (isset($result['payment_url'])) {
    // Optional: Store cart in session (or DB)
    $_SESSION['cart_items'] = $cart_items;
    $_SESSION['user_id']    = $user_id;

    echo json_encode([
        'success'      => true,
        'payment_url'  => $result['payment_url']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['detail'] ?? 'Failed to initiate payment',
        'response' => $result
    ]);
}
