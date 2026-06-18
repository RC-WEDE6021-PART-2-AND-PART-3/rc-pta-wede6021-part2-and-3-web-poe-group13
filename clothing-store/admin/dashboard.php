<?php
/**
 * Admin Dashboard - Past Times Clothing Store
 */
require_once '../includes/DBConn.php';

// Redirect if not admin
if (!isAdminLoggedIn()) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle verify user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_user'])) {
    $user_id = (int)$_POST['user_id'];
    $sql = "UPDATE tblUser SET is_verified = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $success_message = "User verified successfully!";
    } else {
        $error_message = "Error verifying user.";
    }
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    $sql = "DELETE FROM tblUser WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Error deleting user.";
    }
}

// Handle update user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $username = sanitize($conn, $_POST['username']);
    $email = sanitize($conn, $_POST['email']);
    
    $sql = "UPDATE tblUser SET username = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    if ($stmt->execute()) {
        $success_message = "User updated successfully!";
    } else {
        $error_message = "Error updating user.";
    }
}

// Get all users
$users_sql = "SELECT * FROM tblUser ORDER BY created_at DESC";
$users_result = $conn->query($users_sql);
$users = [];
if ($users_result && $users_result->num_rows > 0) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Get pending users count
$pending_sql = "SELECT COUNT(*) as count FROM tblUser WHERE is_verified = 0";
$pending_result = $conn->query($pending_sql);
$pending_count = $pending_result->fetch_assoc()['count'];

// Get total orders
$orders_sql = "SELECT COUNT(*) as count FROM tblOrder";
$orders_result = $conn->query($orders_sql);
$orders_count = $orders_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Past Times</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-header {
            background-color: #2a2a4a;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 20px;
        }
        .admin-header a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background-color: #ff4444;
            border-radius: 5px;
        }
        .dashboard-stats {
            display: flex;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }
        .stat-card {
            background-color: #4a4adb;
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
        }
        .stat-card h3 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .users-table {
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }
        .users-table th, .users-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .users-table th {
            background-color: #4a4adb;
            color: white;
        }
        .users-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn-verify {
            background-color: #00cc00;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-delete {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-edit {
            background-color: #4a4adb;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-verified {
            color: #00cc00;
            font-weight: bold;
        }
        .status-pending {
            color: #ff6600;
            font-weight: bold;
        }
        .edit-form {
            display: none;
            background-color: #f9f9f9;
            padding: 10px;
            margin-top: 10px;
        }
        .edit-form input {
            padding: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" style="margin-left: 15px;">Logout</a>
        </div>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success" style="max-width: 1000px; margin: 20px auto;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-error" style="max-width: 1000px; margin: 20px auto;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3><?php echo count($users); ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card" style="background-color: #ff6600;">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending Verification</p>
        </div>
        <div class="stat-card" style="background-color: #00cc00;">
            <h3><?php echo $orders_count; ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    
    <h2 style="text-align: center; margin: 20px 0;">User Management</h2>
    
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Seller</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php if ($user['is_verified']): ?>
                            <span class="status-verified">Verified</span>
                        <?php else: ?>
                            <span class="status-pending">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $user['is_seller'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if (!$user['is_verified']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" name="verify_user" class="btn-verify">Verify</button>
                            </form>
                        <?php endif; ?>
                        
                        <button class="btn-edit" onclick="toggleEdit(<?php echo $user['user_id']; ?>)">Edit</button>
                        
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <button type="submit" name="delete_user" class="btn-delete">Delete</button>
                        </form>
                        
                        <div id="edit-form-<?php echo $user['user_id']; ?>" class="edit-form">
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="Username" required>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
                                <button type="submit" name="update_user" class="btn-verify">Update</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="../index.php" style="color: #4a4adb; text-decoration: none;">← Back to Store</a>
    </div>
    
    <script>
        function toggleEdit(userId) {
            var form = document.getElementById('edit-form-' + userId);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>
</html>
