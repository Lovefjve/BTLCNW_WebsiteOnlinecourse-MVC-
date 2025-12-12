<?php
// views/instructor/dashboard.php

$root_path = '../../';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Giảng Viên</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fb;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Flash Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .action-card:hover {
            transform: translateY(-5px);
            border-color: #4a6cf7;
            box-shadow: 0 8px 25px rgba(74, 108, 247, 0.15);
            text-decoration: none;
            color: inherit;
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: #f0f5ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #4a6cf7;
        }

        .action-content h3 {
            margin: 0 0 8px;
            color: #2c3e50;
            font-size: 18px;
        }

        .action-content p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            border-top: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-card.courses {
            border-color: #4a6cf7;
        }

        .stat-card.published {
            border-color: #2ecc71;
        }

        .stat-card.pending {
            border-color: #f39c12;
        }

        .stat-card.students {
            border-color: #9b59b6;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .stat-card.courses .stat-icon {
            background: #4a6cf7;
        }

        .stat-card.published .stat-icon {
            background: #2ecc71;
        }

        .stat-card.pending .stat-icon {
            background: #f39c12;
        }

        .stat-card.students .stat-icon {
            background: #9b59b6;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
            font-weight: 700;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Recent Courses */
        .recent-courses {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .section-header h2 {
            color: #2c3e50;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-all-btn {
            background: #f0f5ff;
            color: #4a6cf7;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .view-all-btn:hover {
            background: #4a6cf7;
            color: white;
            text-decoration: none;
        }

        .courses-list {
            display: grid;
            gap: 15px;
        }

        .course-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }


        .course-thumbnail {
            width: 70px;
            height: 45px;
            border-radius: 6px;
            background: #e0e0e0;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .course-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-info {
            flex: 1;
        }

        .course-info h4 {
            margin: 0 0 5px;
            color: #2c3e50;
            font-size: 16px;
        }

        .course-meta {
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-draft {
            background: #e2e3e5;
            color: #383d41;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        .create-btn {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .create-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
            text-decoration: none;
            color: white;
        }

        /* Coming Soon Badge */
        .coming-soon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff9800;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($_SESSION['info']); ?>
                <?php unset($_SESSION['info']); ?>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="?c=course&a=create" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="action-content">
                    <h3>Tạo Khóa Học Mới</h3>
                    <p>Thêm khóa học mới vào hệ thống</p>
                </div>
            </a>

            <a href="?c=course&a=index" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="action-content">
                    <h3>Quản Lý Khóa Học</h3>
                    <p>Xem và chỉnh sửa tất cả khóa học</p>
                </div>
            </a>

            <a href="?c=instructor&a=profile" class="action-card" style="position: relative;">
                <span class="coming-soon">Sắp có</span>
                <div class="action-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="action-content">
                    <h3>Cập Nhật Hồ Sơ</h3>
                    <p>Chỉnh sửa thông tin cá nhân (Đang phát triển)</p>
                </div>
            </a>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card courses">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_courses']; ?></h3>
                    <p>Tổng số khóa học</p>
                </div>
            </div>

            <div class="stat-card published">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['published_courses']; ?></h3>
                    <p>Đã xuất bản</p>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['pending_courses']; ?></h3>
                    <p>Chờ phê duyệt</p>
                </div>
            </div>

            <div class="stat-card students">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_students']; ?></h3>
                    <p>Tổng học viên</p>
                </div>
            </div>
        </div>

        <!-- Recent Courses -->
        <div class="recent-courses">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Khóa học gần đây</h2>
                <a href="?c=course&a=index" class="view-all-btn">Xem tất cả</a>
            </div>

            <?php if (empty($recentCourses)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>Chưa có khóa học nào</h3>
                    <p>Bắt đầu bằng cách tạo khóa học đầu tiên của bạn</p>
                    <a href="?c=course&a=create" class="create-btn">
                        <i class="fas fa-plus-circle"></i> Tạo Khóa Học Đầu Tiên
                    </a>
                </div>
            <?php else: ?>
                <div class="courses-list">
                    <?php foreach ($recentCourses as $course): ?>
                        <a class="course-item">
                            <div class="course-thumbnail">
                                <?php if (!empty($course['image'])): ?>
                                    <img src="assets/uploads/courses/<?php echo htmlspecialchars($course['image']); ?>"
                                        alt="<?php echo htmlspecialchars($course['title']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-book" style="color: #95a5a6;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="course-info">
                                <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                                <div class="course-meta">
                                    <span><?php echo date('d/m/Y', strtotime($course['created_at'])); ?></span>
                                    <span>•</span>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    
                                    if ($course['status'] === 'published') {
                                        $status_class = 'status-published';
                                        $status_text = 'Đã xuất bản';
                                    } elseif ($course['status'] === 'pending') {
                                        $status_class = 'status-pending';
                                        $status_text = 'Chờ duyệt';
                                    } 
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                            </div>
                        
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Prevent click on coming soon items
        document.querySelectorAll('.action-card[href*="profile"]').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Tính năng cập nhật hồ sơ sẽ được phát triển trong tương lai!');
            });
        });
    </script>
</body>

</html>