<?php
// Dashboard cho giảng viên
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Giảng viên</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin: 10px 0;
        }
        a {
            color: #007bff;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            text-decoration: underline;
            color: #0056b3;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px 5px 5px 0;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chào, <?php echo htmlspecialchars($user['username'] ?? 'Giảng viên'); ?></h1>
        <p>Đây là trang Dashboard cho Giảng viên.</p>
        <p><a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-primary">Đăng xuất</a></p>
        <p><a href="<?php echo BASE_URL; ?>/">Về trang chủ</a></p>
    </div>
</body>
</html>
