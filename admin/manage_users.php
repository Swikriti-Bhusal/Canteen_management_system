<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied - Admins only";
    header("Location: login.php");
    exit();
}


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$delete_id]);
    }
    header("Location: manage_users.php");
    exit();
}


$stmt = $conn->query("SELECT * FROM users WHERE role != 'admin'");
$users = $stmt->fetchAll(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --danger-color: #f72585;
            --success-color: #4cc9f0;
            --text-color: #2b2d42;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .no-users {
            text-align: center;
            padding: 20px;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }
        
        .users-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: var(--white);
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .users-table th {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 15px;
            text-align: left;
            font-weight: 500;
        }
        
        .users-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .users-table tr:last-child td {
            border-bottom: none;
        }
        
        .users-table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .delete-btn {
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
        
        .delete-btn:hover {
            background-color: var(--danger-color);
            color: var(--white);
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .admin-badge {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }
        
        @media (max-width: 768px) {
            .users-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        
        <?php if (empty($users)): ?>
            <div class="no-users">
                <p>No regular users found in the system.</p>
            </div>
        <?php else: ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone_no']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <a class="action-btn delete-btn" 
                                   href="manage_users.php?delete_id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>