<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require('../config.php');

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    $_SESSION['error'] = "Invalid order ID";
    header("Location: orders.php");
    exit();
}

// Get order details (with user verification)
$order_query = $conn->prepare("
    SELECT o.*, u.username, u.email, u.phone, u.address
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$order_query->bind_param("ii", $order_id, $_SESSION['user_id']);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['error'] = "Order not found or doesn't belong to you";
    header("Location: orders.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items - CORRECTED QUERY
$items_query = $conn->prepare("
    SELECT 
        oi.id AS order_item_id,
        oi.quantity,
        oi.price AS ordered_price,
        fi.id AS food_id,
        fi.name,
        fi.image,
        fi.description
    FROM order_items oi
    JOIN food_items fi ON oi.food_id = fi.id
    WHERE oi.order_id = ?
");

// Debugging: Check if query preparation failed
if (!$items_query) {
    die("Query preparation failed: " . $conn->error);
}

$items_query->bind_param("i", $order_id);

// Debugging: Check if query execution failed
if (!$items_query->execute()) {
    die("Query execution failed: " . $items_query->error);
}

$items_result = $items_query->get_result();

// Debugging: Check if getting results failed
if (!$items_result) {
    die("Getting results failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= $order['id'] ?> Details - Canteen Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .order-status {
            @apply px-3 py-1 rounded-full text-sm font-medium;
        }
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }
        .status-preparing {
            @apply bg-blue-100 text-blue-800;
        }
        .status-ready {
            @apply bg-purple-100 text-purple-800;
        }
        .status-delivered {
            @apply bg-green-100 text-green-800;
        }
        .status-cancelled {
            @apply bg-red-100 text-red-800;
        }
        .action-btn {
            @apply px-4 py-2 rounded-md font-medium transition-colors;
        }
        .back-btn {
            @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
        }
        .cancel-btn {
            @apply bg-red-100 text-red-800 hover:bg-red-200;
        }
        .item-image {
            @apply w-16 h-16 object-cover rounded-md;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="orders.php" class="action-btn back-btn mr-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Order #<?= $order['id'] ?></h1>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-wrap justify-between items-start">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                        <p class="text-sm text-gray-500">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div>
                        <span class="order-status status-<?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Information -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-2">Customer Information</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="mb-1"><span class="font-medium">Name:</span> <?= htmlspecialchars($order['username']) ?></p>
                        <p class="mb-1"><span class="font-medium">Email:</span> <?= htmlspecialchars($order['email']) ?></p>
                        <p class="mb-1"><span class="font-medium">Phone:</span> <?= htmlspecialchars($order['phone']) ?></p>
                        <p><span class="font-medium">Address:</span> <?= htmlspecialchars($order['address']) ?></p>
                    </div>
                </div>

                <!-- Order Totals -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-2">Order Totals</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between mb-2">
                            <span>Subtotal:</span>
                            <span>Rs.<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Delivery Fee:</span>
                            <span>Rs.0.00</span>
                        </div>
                        <div class="flex justify-between font-medium text-lg border-t border-gray-200 pt-2 mt-2">
                            <span>Total:</span>
                            <span>Rs.<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
            </div>

            <div class="divide-y divide-gray-200">
                <?php if ($items_result->num_rows > 0): ?>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <div class="p-6 flex flex-wrap items-start">
                            <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md mr-4">
                                <?php if ($item['image']): ?>
                                    <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-utensils text-xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex-1 min-w-0 mr-4">
                                <h3 class="text-md font-medium text-gray-900 mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="text-sm text-gray-500 truncate"><?= htmlspecialchars($item['description']) ?></p>
                            </div>

                            <div class="flex flex-col items-end">
                                <p class="text-md font-medium">Rs.<?= number_format($item['ordered_price'], 2) ?></p>
                                <p class="text-sm text-gray-500">Qty: <?= $item['quantity'] ?></p>
                                <p class="text-md font-medium mt-1">Rs.<?= number_format($item['ordered_price'] * $item['quantity'], 2) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        No items found in this order
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Actions -->
        <div class="mt-6 flex justify-end">
            <?php if (in_array($order['status'], ['pending', 'preparing'])): ?>
                <form method="POST" action="orders.php" class="inline">
                    <input type="hidden" name="cancel_order" value="<?= $order['id'] ?>">
                    <button type="submit" class="action-btn cancel-btn"
                        onclick="return confirm('Are you sure you want to cancel order #<?= $order['id'] ?>?')">
                        <i class="fas fa-times mr-2"></i> Cancel Order
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>