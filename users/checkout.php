<?php
session_start();
require_once '../config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
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

// Generate unique order ID
$temp_order_id = 'ORD_' . uniqid('', true) . '_' . $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }
        li {
            background: #f9f9f9;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
        }
        h3 {
            text-align: right;
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #111827;
        }
        button {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .btn-khalti {
            background-color: #5C2D91;
            color: white;
            margin-bottom: 10px;
        }
        .btn-khalti:hover {
            background-color: #4a2473;
        }
        .btn-cod {
            background-color: #ff6b00;
            color: white;
        }
        .btn-cod:hover {
            background-color: #e05d00;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Confirm Your Order</h2>
    <ul>
        <?php foreach ($cartItems as $item): ?>
            <li>
                <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                <span>Rs <?= $item['price'] * $item['quantity'] ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <h3>Total: Rs <?= $total ?></h3>

    <!-- Khalti Payment Button -->
    <button id="pay-khalti" class="btn-khalti">Pay with Khalti</button>

    <!--  Cash on Delivery Form -->
    <form action="../payment/process_cod.php" method="POST">
        <input type="hidden" name="payment_method" value="cod">
        <input type="hidden" name="total_amount" value="<?= $total ?>">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="cart_items" value="<?= htmlspecialchars(json_encode($cartItems)) ?>">
        <button type="submit" class="btn-cod">Cash on Delivery</button>
    </form>
</div>

<!--  Khalti fetch() Script -->
<script>
document.getElementById("pay-khalti").addEventListener("click", function () {
    this.disabled = true;

    fetch("../payment/initiate_khalti.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            amount: <?= $total * 100 ?>,
            order_id: "<?= $temp_order_id ?>",
            user_id: <?= $_SESSION['user_id'] ?>,
            cart_items: <?= json_encode($cartItems) ?>
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            alert(" Khalti Error: " + (data.message || "Something went wrong."));
            console.log(data.response || data);
            document.getElementById("pay-khalti").disabled = false;
        }
    })
    .catch(err => {
        alert(" Network Error: " + err.message);
        console.error(err);
        document.getElementById("pay-khalti").disabled = false;
    });
});
</script>
</body>
</html>
