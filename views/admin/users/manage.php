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
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
        }
        td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            color: #555;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .btn {
            padding: 8px 14px;
            margin: 2px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-create {
            background-color: #007bff;
            color: white;
        }
        .btn-create:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-edit:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        .role-0 {
            background-color: #e3f2fd;
        }
        .role-0:hover {
            background-color: #bbdefb;
        }
        .role-1 {
            background-color: #fff3e0;
        }
        .role-1:hover {
            background-color: #ffe0b2;
        }
        .role-2 {
            background-color: #f3e5f5;
        }
        .role-2:hover {
            background-color: #e1bee7;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            flex: 1;
        }
            .top-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:10px; }
            .btn-secondary { background:#6c757d; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; }
            .btn-secondary:hover { background:#5a6268; }
            .btn-logout { background:#dc3545; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; }
            .btn-logout:hover { background:#c82333; }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .success-message::before {
            content: "✓ ";
            font-weight: bold;
            margin-right: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            text-decoration: underline;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quản lý Người dùng</h1>
            <a href="<?php echo BASE_URL; ?>/admin/createUser" class="btn btn-create">+ Tạo tài khoản mới</a>
        </div>
        
        <div class="top-actions">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">← Về Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-logout">🚪 Đăng xuất</a>
        </div>

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
