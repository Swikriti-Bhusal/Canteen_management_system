<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require('../config.php');

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['cancel_order']);
    $user_id = $_SESSION['user_id'];
    
    // Verify order belongs to user and is cancellable (using prepared statement)
    $check_query = "SELECT id, status FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        if (in_array($order['status'], ['pending', 'preparing'])) {
            $update_query = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $order_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "Order #$order_id has been cancelled successfully.";
            } else {
                $_SESSION['error'] = "Failed to cancel order. Please try again.";
            }
            $update_stmt->close();
        } else {
            $_SESSION['error'] = "Cannot cancel order - status is already ".ucfirst($order['status']);
        }
    } else {
        $_SESSION['error'] = "Order not found or doesn't belong to you.";
    }
    $stmt->close();
    header("Location: orders.php");
    exit();
}

// Get user information
$user_query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$user_query->bind_param("i", $_SESSION['user_id']);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Get orders with proper item counts and totals (using prepared statement)
$query = "SELECT 
            o.id, 
            o.created_at, 
            o.status,
            o.total_amount,
            COUNT(oi.id) as item_count
          FROM orders o
          LEFT JOIN order_items oi ON o.id = oi.order_id
          WHERE o.user_id = ?
          GROUP BY o.id
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Canteen Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .order-status {
            @apply px-2 py-1 text-xs font-medium rounded-full;
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
            @apply px-3 py-1 rounded text-sm transition-colors;
        }
        .view-btn {
            @apply bg-blue-100 text-blue-800 hover:bg-blue-200;
        }
        .cancel-btn {
            @apply bg-red-100 text-red-800 hover:bg-red-200;
        }
        .order-details {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
            <div class="text-right">
                <p class="font-medium"><?= htmlspecialchars($user['username']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <div class="flex justify-between items-center">
                    <p><?= htmlspecialchars($_SESSION['message']) ?></p>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <div class="flex justify-between items-center">
                    <p><?= htmlspecialchars($_SESSION['error']) ?></p>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($orders_result->num_rows > 0): ?>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">#<?= $order['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">Rs.<?= number_format($order['total_amount'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="order-status status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="action-btn view-btn">
                                     View </a>
                                    <?php if (in_array($order['status'], ['pending', 'preparing'])): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="cancel_order" value="<?= $order['id'] ?>">
                                            <button type="submit" class="action-btn cancel-btn"
                                                onclick="return confirm('Are you sure you want to cancel order #<?= $order['id'] ?>?')">
                                                <i class="fas fa-times mr-1"></i> Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-clipboard-list text-5xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
                <a href="menu.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-utensils mr-2"></i> Browse Menu
                </a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>