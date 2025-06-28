<?php
session_start();
require_once '../config.php';

// Get POST data
$amount = $_POST['amount'];
$order_id = $_POST['order_id'];
$user_id = $_POST['user_id'];
$cart_items = $_POST['cart_items'];

// OPTIONAL: Fetch name/email/phone from session if available
$name = $_SESSION['user_name'] ?? '';
$email = $_SESSION['user_email'] ?? '';
$phone = $_SESSION['user_phone'] ?? '';

// If not set in session, fetch from DB
if (empty($name) || empty($email) || empty($phone)) {
    $stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $name = $user['username'];  // corrected here
        $email = $user['email'];
        $phone = $user['phone'];
    }
    $stmt->close();
}

// Prepare data to send to Khalti
$data = [
    'return_url' => 'http://localhost/cms/payment/khalti_callback.php',
    'website_url' => 'http://localhost/cms',
    'amount' => (int) $amount,
    'purchase_order_id' => $order_id,
    'purchase_order_name' => 'Canteen Order',
    'customer_info' => [
        'name' => $name,
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
    CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/initiate/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => $headers
]);

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);

if (isset($result['payment_url'])) {
    $_SESSION['cart_items'] = $cart_items;
    $_SESSION['user_id'] = $user_id;
    echo json_encode(['payment_url' => $result['payment_url']]);
exit;
    // header("Location: " . $result['payment_url']);
    // exit;
} else {
    echo "<pre>Initiation Failed:\n";
    print_r($result);
    echo "</pre>";
}
