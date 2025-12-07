<?php
// Trang quản lý người dùng cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-create {
            background-color: #007bff;
            color: white;
        }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        .role-0 {
            background-color: #e3f2fd;
        }
        .role-1 {
            background-color: #fff3e0;
        }
        .role-2 {
            background-color: #f3e5f5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quản lý Người dùng</h1>
            <a href="<?php echo BASE_URL; ?>/admin/createUser" class="btn btn-create">+ Tạo tài khoản mới</a>
        </div>
        
        <p><a href="<?php echo BASE_URL; ?>/admin/dashboard">← Về Dashboard</a> | <a href="<?php echo BASE_URL; ?>/auth/logout">Đăng xuất</a></p>

        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] === 'created'): ?>
                <div class="success-message">Tạo tài khoản thành công!</div>
            <?php elseif ($_GET['success'] === 'updated'): ?>
                <div class="success-message">Cập nhật tài khoản thành công!</div>
            <?php elseif ($_GET['success'] === 'deleted'): ?>
                <div class="success-message">Xóa tài khoản thành công!</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Họ tên</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $roleText = '';
                        if ($u['role'] == 0) $roleText = 'Học viên';
                        elseif ($u['role'] == 1) $roleText = 'Giảng viên';
                        elseif ($u['role'] == 2) $roleText = 'Admin';
                        
                        $roleClass = 'role-' . $u['role'];
                    ?>
                        <tr class="<?php echo $roleClass; ?>">
                            <td><?php echo htmlspecialchars($u['id']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                            <td><?php echo $roleText; ?></td>
                            <td><?php echo htmlspecialchars($u['created_at'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/admin/editUser?id=<?php echo $u['id']; ?>" class="btn btn-edit">Chỉnh sửa</a>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/deleteUser" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?');">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Không có người dùng nào trong hệ thống.</p>
        <?php endif; ?>
    </div>
</body>
</html>
