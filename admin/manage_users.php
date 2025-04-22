<?php
session_start();

// Redirect to login if not authenticated as admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

require('../config.php');

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    
    // Prevent deleting yourself
    if ($user_id != $_SESSION['user_id']) {
        $delete_query = "DELETE FROM users WHERE id = $user_id";
        mysqli_query($conn, $delete_query);
        $_SESSION['message'] = "User deleted successfully";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['error'] = "You cannot delete your own account";
    }
}

// Get all users except the current admin
$query = "SELECT id, username, email, level, created_at FROM users WHERE id != ".$_SESSION['user_id']." ORDER BY created_at DESC";
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: grid;
            grid-template-rows: auto auto 1fr;
            background: #f5f5f5;
        }
        
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
        
        .page-title {
            padding: 20px 0;
            text-align: center;
            background: white;
        }
        
        .content {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            height: 100%;
            overflow: auto;
        }
        
        .users-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .users-table {
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
        
        .level-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .level-admin { background: #D4EDDA; color: #155724; }
        .level-user { background: #FFF3CD; color: #856404; }
    </style>
</head>
<body>
    <header class="admin-header">
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart mr-2"></i>Orders</a></li>
            <li><a href="manage_menu.php"><i class="fas fa-utensils mr-2"></i>Menu</a></li>
            <li><a href="manage_users.php" class="active"><i class="fas fa-users mr-2"></i>Users</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
        </ul>
    </header>

    <div class="page-title">
        <h1 class="text-2xl font-bold">Manage Users</h1>
        <p class="text-gray-600">View and manage all system users</p>
    </div>

    <div class="content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>


            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>USERNAME</th>
                            <th>EMAIL</th>
                            <th>ROLE</th>
                            <th>CREATED AT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="level-badge level-<?= $user['level'] == 1 ? 'admin' : 'user' ?>">
                                    <?= $user['level'] == 1 ? 'Admin' : 'User' ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y h:i A', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Confirm before deleting
        document.querySelectorAll('a[href*="delete"]').forEach(link => {
            link.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this user?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>