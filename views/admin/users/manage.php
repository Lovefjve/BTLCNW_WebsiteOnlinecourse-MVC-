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
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-activate {
            background-color: #28a745;
            color: white;
        }
        .btn-deactivate {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete {
            background-color: #6c757d;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản lý Người dùng</h1>
        <p><a href="<?php echo BASE_URL; ?>/admin/dashboard">← Về Dashboard</a> | <a href="<?php echo BASE_URL; ?>/auth/logout">Đăng xuất</a></p>

        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Họ tên</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
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
                        
                        $statusClass = ($u['status'] ?? 'active') === 'active' ? 'status-active' : 'status-inactive';
                        $statusText = ($u['status'] ?? 'active') === 'active' ? 'Kích hoạt' : 'Vô hiệu hóa';
                        $roleClass = 'role-' . $u['role'];
                    ?>
                        <tr class="<?php echo $roleClass; ?>">
                            <td><?php echo htmlspecialchars($u['id']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                            <td><?php echo $roleText; ?></td>
                            <td class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></td>
                            <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="btn btn-deactivate" formaction="<?php echo BASE_URL; ?>/admin/updateUserStatus">Vô hiệu hóa</button>
                                    <?php else: ?>
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-activate" formaction="<?php echo BASE_URL; ?>/admin/updateUserStatus">Kích hoạt</button>
                                    <?php endif; ?>
                                </form>
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
