<?php
// Hiển thị dashboard cho học viên (tạm thời)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Học viên</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Chào, <?php echo htmlspecialchars($user['username'] ?? 'Học viên'); ?></h1>
        <p>Đây là trang Dashboard cho Học viên (tạm thời).</p>
        <p><a href="<?php echo BASE_URL; ?>/auth/logout">Đăng xuất</a></p>
        <p><a href="<?php echo BASE_URL; ?>/">Về trang chủ</a></p>
    </div>
</body>
</html>
