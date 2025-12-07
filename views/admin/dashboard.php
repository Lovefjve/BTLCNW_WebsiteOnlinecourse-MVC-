<?php
// Dashboard cho quản trị viên (tạm thời)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Chào, <?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></h1>
        <p>Đây là trang Dashboard cho Quản trị viên.</p>
        
        <h2>Chức năng Quản lý</h2>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/admin/users">Quản lý người dùng</a> - Xem, thêm, chỉnh sửa, xóa người dùng</li>
        </ul>

        <p><a href="<?php echo BASE_URL; ?>/auth/logout">Đăng xuất</a></p>
        <p><a href="<?php echo BASE_URL; ?>/">Về trang chủ</a></p>
    </div>
</body>
</html>
