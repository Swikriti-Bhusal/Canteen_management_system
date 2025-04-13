<?php
session_start();
include('../config.php'); // Make sure this returns a valid $pdo PDO connection

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete user if delete request is sent
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: manage_users.php");
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background: #f2f2f2;
        }

        a.delete-btn {
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Manage Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $row) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <?php if ($row['role'] != 'admin') { ?>
                        <a class="delete-btn" href="manage_users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure to delete this user?');">Delete</a>
                    <?php } else { echo 'Admin'; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
