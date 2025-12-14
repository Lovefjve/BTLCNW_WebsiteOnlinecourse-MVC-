<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Quản Lý Khóa Học</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 20px;
        }
        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .welcome-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .welcome-box h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .welcome-box p {
            color: #666;
            margin: 10px 0;
        }
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }
        .auth-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .auth-box h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .auth-box p {
            color: #666;
            margin-bottom: 15px;
        }
        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .role-card {
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .role-card h3 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .role-card p {
            color: #555;
            font-size: 14px;
            margin: 0;
        }
        .role-0 {
            background-color: #e3f2fd;
            border-left-color: #2196F3;
        }
        .role-1 {
            background-color: #fff3e0;
            border-left-color: #FF9800;
        }
        .role-2 {
            background-color: #f3e5f5;
            border-left-color: #9C27B0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
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
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        .footer-text {
            text-align: center;
            margin-top: 40px;
            color: #999;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .auth-grid {
                grid-template-columns: 1fr;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Hệ Thống Quản Lý Khóa Học Online</h1>
        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">Nền tảng học tập hiện đại cho học viên và giảng viên</p>

        <?php if (Auth::isLoggedIn()): ?>
            <?php $user = Auth::getUser(); ?>
            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2>Chào, <?php echo htmlspecialchars($user['username']); ?></h2>
                <p>Bạn đã đăng nhập thành công!</p>
                
                <?php if ($user['role'] == 0): ?>
                    <p><a href="<?php echo BASE_URL; ?>/student/dashboard" class="btn btn-primary">Vào Dashboard Học Viên</a></p>
                <?php elseif ($user['role'] == 1): ?>
                    <p><a href="<?php echo BASE_URL; ?>/instructor/dashboard" class="btn btn-primary">Vào Dashboard Giảng Viên</a></p>
                <?php elseif ($user['role'] == 2): ?>
                    <p><a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-primary">Vào Dashboard Quản Trị</a></p>
                <?php endif; ?>
                
                <p><a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-success">Đăng Xuất</a></p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px;">
                <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
                    <h3>👤 Đã có tài khoản?</h3>
                    <p>Đăng nhập để truy cập các khóa học của bạn</p>
                    <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-primary">Đăng Nhập</a>
                </div>
                <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
                    <h3>✨ Chưa có tài khoản?</h3>
                    <p>Đăng ký ngay để bắt đầu học tập</p>
                    <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-success">Đăng Ký</a>
                </div>
            </div>
        <?php endif; ?>

        <h2>Các Vai Trò Trong Hệ Thống</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #2196F3;">
                <h3>📚 Học Viên</h3>
                <p>Tham gia khóa học, theo dõi tiến độ, hoàn thành bài tập</p>
            </div>
            <div style="background: #fff3e0; padding: 20px; border-radius: 8px; border-left: 4px solid #FF9800;">
                <h3>👨‍🏫 Giảng Viên</h3>
                <p>Tạo khóa học, quản lý học viên, đánh giá bài tập</p>
            </div>
            <div style="background: #f3e5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #9C27B0;">
                <h3>⚙️ Quản Trị</h3>
                <p>Quản lý người dùng, khóa học, báo cáo hệ thống</p>
            </div>
        </div>

        <p style="text-align: center; margin-top: 40px; color: #999;">
            © 2024 Hệ Thống Quản Lý Khóa Học Online. Tất cả quyền được bảo lưu.
        </p>
    </div>
</body>
</html>