<?php
// Dashboard cho quản trị viên
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-left: 5px solid #007bff;
        }
        
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 0;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
            cursor: pointer;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            font-size: 32px;
            padding: 20px;
            text-align: center;
        }
        
        .card-content {
            padding: 20px;
            text-align: center;
        }
        
        .card-content h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 8px;
        }
        
        .card-content p {
            color: #666;
            font-size: 13px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .card-link {
            display: inline-block;
            padding: 10px 18px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .card-link:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .card-link.disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }
        
        /* Card color variations */
        .card-users {
            border-left-color: #2196F3;
        }
        
        .card-users .card-icon {
            background-color: #e3f2fd;
        }
        
        .card-categories {
            border-left-color: #FF9800;
        }
        
        .card-categories .card-icon {
            background-color: #fff3e0;
        }
        
        .card-stats {
            border-left-color: #4CAF50;
        }
        
        .card-stats .card-icon {
            background-color: #e8f5e9;
        }
        
        .card-approvals {
            border-left-color: #9C27B0;
        }
        
        .card-approvals .card-icon {
            background-color: #f3e5f5;
        }
        
        .footer-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .footer-section a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .footer-section a:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .footer-section a.logout {
            background-color: #dc3545;
        }
        
        .footer-section a.logout:hover {
            background-color: #c82333;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .header-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h1>🎯 Dashboard Quản trị Viên</h1>
            <p class="subtitle">Chào, <strong><?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></strong> - Quản lý toàn bộ hệ thống</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- Quản lý người dùng -->
            <div class="dashboard-card card-users">
                <div class="card-icon">👥</div>
                <div class="card-content">
                    <h3>Quản lý Người dùng</h3>
                    <p>Quản lý tài khoản sinh viên, giáo viên và quản trị viên trong hệ thống</p>
                    <a href="<?php echo BASE_URL; ?>/admin/users" class="card-link">Mở →</a>
                </div>
            </div>
            
            <!-- Quản lý danh mục khóa học -->
            <div class="dashboard-card card-categories">
                <div class="card-icon">📚</div>
                <div class="card-content">
                    <h3>Quản lý Danh mục</h3>
                    <p>Tạo, sửa, xóa các danh mục khóa học để tổ chức nội dung</p>
                    <button class="card-link disabled">Sắp ra mắt 🔒</button>
                </div>
            </div>
            
            <!-- Xem thống kê -->
            <div class="dashboard-card card-stats">
                <div class="card-icon">📊</div>
                <div class="card-content">
                    <h3>Thống kê Hệ thống</h3>
                    <p>Xem báo cáo sử dụng, thống kê người dùng và hiệu suất khóa học</p>
                    <button class="card-link disabled">Sắp ra mắt 🔒</button>
                </div>
            </div>
            
            <!-- Duyệt phê duyệt khóa học -->
            <div class="dashboard-card card-approvals">
                <div class="card-icon">✅</div>
                <div class="card-content">
                    <h3>Phê duyệt Khóa học</h3>
                    <p>Xem và duyệt các khóa học mới được tạo bởi giáo viên</p>
                    <button class="card-link disabled">Sắp ra mắt 🔒</button>
                </div>
            </div>
        </div>
        
        <div class="footer-section">
            <a href="<?php echo BASE_URL; ?>/">🏠 Trang chủ</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="logout">🚪 Đăng xuất</a>
        </div>
    </div>
</body>
</html>
