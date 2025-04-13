<?php
session_start();

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin/login.php");
    exit();
}
require('../config.php');


// Get stats for dashboard
try {
    // Total orders
    $orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    
    // Revenue
    $revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'")->fetchColumn();
    
    // Popular items
    $popularItems = $pdo->query("
        SELECT fi.name, SUM(oi.quantity) as total_ordered 
        FROM order_items oi
        JOIN food_item fi ON oi.food_id = fi.id
        GROUP BY fi.name
        ORDER BY total_ordered DESC
        LIMIT 5
    ")->fetchAll();
    
    // Recent orders
    $recentOrders = $pdo->query("
        SELECT o.id, u.username, o.total_amount, o.status, o.created_at
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #2c3e50;
            color: white;
        }
        .user-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar {
            transition: all 0.3s;
        }
        .sidebar-collapsed {
            width: 80px;
        }
        .sidebar-collapsed .nav-text {
            display: none;
        }
        .content {
            transition: all 0.3s;
        }
        .content-expanded {
            margin-left: 80px;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="admin-header">
        <h1>Admin Dashboard</h1>
        <div class="user-display">
            <div class="user-avatar">
                <i class="fas fa-user-cog"></i> <!-- Admin specific icon -->
            </div>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../auth/logout.php" style="color: white; margin-left: 15px;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar bg-blue-800 text-white w-64 fixed h-full flex flex-col sidebar">
            <div class="p-4 text-center border-b border-blue-700">
                <h1 class="text-xl font-bold">Canteen Admin</h1>
            </div>
            <nav class="flex-grow p-4">
                <ul>
                    <li class="mb-2">
                        <a href="index.php" class="flex items-center p-2 rounded hover:bg-blue-700 bg-blue-700">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="orders.php" class="flex items-center p-2 rounded hover:bg-blue-700">
                            <i class="fas fa-shopping-cart mr-3"></i>
                            <span class="nav-text">Orders</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../admin/manage_menu.php" class="flex items-center p-2 rounded hover:bg-blue-700">
                            <i class="fas fa-utensils mr-3"></i>
                            <span class="nav-text">Menu Items</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../admin/manage_users.php" class="flex items-center p-2 rounded hover:bg-blue-700">
                            <i class="fas fa-users mr-3"></i>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="reports.php" class="flex items-center p-2 rounded hover:bg-blue-700">
                            <i class="fas fa-chart-bar mr-3"></i>
                            <span class="nav-text">Reports</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../auth/logout.php" class="flex items-center p-2 rounded hover:bg-blue-700">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="content flex-grow ml-64 p-8 content">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                <p class="text-gray-600">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Orders</p>
                            <h3 class="text-2xl font-bold"><?= number_format($orders) ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-rupee-sign text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Revenue</p>
                            <h3 class="text-2xl font-bold">Rs<?= number_format($revenue, 0) ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                            <i class="fas fa-utensils text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Menu Items</p>
                            <h3 class="text-2xl font-bold"><?= number_format($pdo->query("SELECT COUNT(*) FROM food_items")->fetchColumn()) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Popular Items -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Orders</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($recentOrders as $order): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="order_details.php?id=<?= $order['id'] ?>" class="text-blue-600 hover:underline">#<?= $order['id'] ?></a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['username']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                               ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t text-right">
                        <a href="orders.php" class="text-blue-600 hover:underline">View All Orders</a>
                    </div>
                </div>

                
             
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar collapse
        document.getElementById('toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('sidebar-collapsed');
            document.querySelector('.content').classList.toggle('content-expanded');
        });
    </script>
</body>
</html>