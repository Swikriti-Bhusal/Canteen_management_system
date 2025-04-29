<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    header("Location: ../admin/login.php");
    exit();
}

require('../config.php');

// Get all orders with user details and optional status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT o.*, u.username, u.email 
          FROM orders o
          JOIN users u ON o.user_id = u.id";

if ($status_filter != 'all') {
    $query .= " WHERE o.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

if (!empty($search_query)) {
    $query .= ($status_filter != 'all' ? " AND" : " WHERE") . 
              " (o.id LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%' OR 
                 u.username LIKE '%" . mysqli_real_escape_string($conn, $search_query) . "%')";
}

$query .= " ORDER BY o.created_at DESC";

$orders = [];
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Get counts for each status
$status_counts = [
    'all' => 0,
    'pending' => 0,
    'completed' => 0,
    'delivered' => 0
];

$count_result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
while ($row = mysqli_fetch_assoc($count_result)) {
    $status_counts[$row['status']] = $row['count'];
    $status_counts['all'] += $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    /* Basic reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Full page layout */
    body {
        font-family: Arial, sans-serif;
        height: 100vh;
        display: grid;
        grid-template-rows: auto auto 1fr;
        background: #f5f5f5;
    }

    /* Header styling */
    .admin-header {
        background: #2c3e50;
        padding: 0 20px;
    }

    .admin-header ul {
        display: flex;
        list-style: none;
        height: 60px;
        max-width: 1200px;
        margin: 0 auto;
        align-items: center;
    }

    .admin-header li {
        margin-right: 20px;
    }

    .admin-header a {
        color: white;
        text-decoration: none;
        padding: 10px;
        display: block;
    }

    /* Page title area */
    .mb-6 {
        padding: 20px 0;
        text-align: center;
        background: white;
    }

    /* Main content container */
    .content {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 20px;
        height: 100%;
        overflow: auto;
    }

    /* Orders table container */
    .orders-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* Table styling */
    .orders-table {
        flex: 1;
        overflow: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        background: #f9f9f9;
        position: sticky;
        top: 0;
    }

    tr:hover {
        background: #f5f5f5;
    }

    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending { background: #FFF3CD; color: #856404; }
    .status-completed { background: #D4EDDA; color: #155724; }
    .status-delivered { background: #CCE5FF; color: #004085; }
    
    /* Action buttons */
    .action-btn {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        margin-right: 5px;
        border: none;
        transition: background-color 0.3s;
    }
    .pending-btn { 
        background: #FFC107; 
        color: #000; 
    }
    .pending-btn:hover { 
        background: #E0A800; 
    }
    .complete-btn { 
        background: #28A745; 
        color: white; 
    }
    .complete-btn:hover { 
        background: #218838; 
    }
    .deliver-btn { 
        background: #17A2B8; 
        color: white; 
    }
    .deliver-btn:hover { 
        background: #138496; 
    }
</style>
</head>
<body class="bg-gray-100">
    <header class="admin-header">
    <ul>
        <li><a href="index.php" class="active">Dashboard</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="manage_menu.php">Menu</a></li>
        <li><a href="manage_users.php">Users</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
    </header>

    <div class="flex h-screen">
        <!-- Main Content -->
        <div class="content flex-grow ml-64 p-8">
            <div class="mb-6">
                <h1 style="text-align: center;" class="text-3xl font-bold text-gray-800">Manage Orders</h1>
                <p style="text-align: center;" class="text-gray-600">View and manage all customer orders</p>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="?status=all" 
                           class="<?= $status_filter == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?> px-3 py-1 rounded-full text-sm">
                            All (<?= $status_counts['all'] ?>)
                        </a>
                        <a href="?status=pending" 
                           class="<?= $status_filter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200' ?> px-3 py-1 rounded-full text-sm">
                            Pending (<?= $status_counts['pending'] ?>)
                        </a>
                        <a href="?status=completed" 
                           class="<?= $status_filter == 'completed' ? 'bg-green-500 text-white' : 'bg-gray-200' ?> px-3 py-1 rounded-full text-sm">
                            Completed (<?= $status_counts['completed'] ?>)
                        </a>
                        <a href="?status=delivered" 
                           class="<?= $status_filter == 'delivered' ? 'bg-blue-500 text-white' : 'bg-gray-200' ?> px-3 py-1 rounded-full text-sm">
                            Delivered (<?= $status_counts['delivered'] ?>)
                        </a>
                    </div>
                    
                    <form method="get" class="flex gap-2">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        <input type="text" name="search" placeholder="Search orders..." 
                               value="<?= htmlspecialchars($search_query) ?>"
                               class="border rounded px-3 py-1 w-full md:w-64">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search_query)): ?>
                            <a href="?status=<?= htmlspecialchars($status_filter) ?>" class="bg-gray-200 px-3 py-1 rounded flex items-center">
                                Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="text-blue-600 hover:underline">
                                        #<?= $order['id'] ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium"><?= htmlspecialchars($order['username']) ?></div>
                                    <div class="text-gray-500 text-sm"><?= htmlspecialchars($order['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    Rs<?= number_format($order['total_amount'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <?php if ($order['status'] != 'pending'): ?>
                                            <form method="post" action="update_order_status.php" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="action-btn pending-btn">
                                                    <i class="fas fa-clock"></i> Pending
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status'] != 'completed'): ?>
                                            <form method="post" action="update_order_status.php" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="action-btn complete-btn">
                                                    <i class="fas fa-check"></i> Complete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status'] != 'delivered'): ?>
                                            <form method="post" action="update_order_status.php" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="action-btn deliver-btn">
                                                    <i class="fas fa-truck"></i> Deliver
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-shopping-cart fa-3x mb-4"></i>
                        <p class="text-xl">No orders found</p>
                        <?php if (!empty($search_query) || $status_filter != 'all'): ?>
                            <p class="mt-2">
                                <a href="orders.php" class="text-blue-600 hover:underline">
                                    Clear filters
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>