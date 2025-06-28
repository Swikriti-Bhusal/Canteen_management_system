<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../users/login.php");
    exit();
}

// Get cart items with food details for the current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id as cart_id, c.quantity, c.created_at, 
               f.id as food_id, f.name, f.price, f.image 
        FROM cart c
        JOIN food_items f ON c.food_id = f.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);

if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<?php include '../includes/header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .cart-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cart-table th, .cart-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .cart-table th {
            background-color: #f8f8f8;
        }
        .cart-item-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
        }
        .quantity-btn {
            padding: 5px 10px;
            background: #eee;
            border: none;
            cursor: pointer;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            margin: 0 5px;
        }
        .remove-btn {
            color: #ff6b00;
            text-decoration: none;
        }
        .cart-summary {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .summary-box {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #ff6b00;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body><div class="container">
    <div class="cart-header">
        <h1>Your Shopping Cart</h1>
    </div>

      <?php if (!empty($cartItems)): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <img src="../../cms/uploads/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                     class="cart-item-img">
                                <span style="margin-left: 10px;"><?= htmlspecialchars($item['name']) ?></span>
                            </div>
                        </td>
                        <td>Rs<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <div class="quantity-control">
                                <form action="../users/update_cart.php" method="post" style="display: flex;">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <button type="submit" name="action" value="decrease" class="quantity-btn">-</button>
                                    <span class="quantity-input"><?= $item['quantity'] ?></span>
                                    <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
                                </form>
                            </div>
                        </td>
                        <td>Rs<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        <td>
                            <a href="../users/remove_from_cart.php?id=<?= $item['cart_id'] ?>" 
                               class="remove-btn"
                               onclick="return confirm('Are you sure you want to remove this item?')">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="summary-box">
                <h3>Order Summary</h3>
                <p>Total: Rs<?= number_format($total, 2) ?></p>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                <a href="/cms/users/menu.php" class="checkout-btn" style="background: #6c757d; margin-top: 10px;">
                    Back To Menu
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="/cms/users/menu.php" class="checkout-btn" style="background: #6c757d; display: inline-block; width: auto;">
                Browse Menu
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Simple confirmation for remove action
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to remove this item?')) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>


