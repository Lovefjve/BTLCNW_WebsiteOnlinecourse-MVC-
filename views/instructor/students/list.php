<?php
// views/instructor/students/list.php

$root_path = '../../';

if (!isset($course)) {
    die("Thiếu thông tin cần thiết để hiển thị trang");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Học viên - <?php echo htmlspecialchars($course['title'] ?? 'Khóa học'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #4a6cf7;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-info {
            flex: 1;
        }

        .header-info h2 {
            color: #4a6cf7;
            font-size: 18px;
            font-weight: 600;
            margin: 5px 0 0 0;
        }

        .header-info p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .btn {
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

        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-top: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total {
            border-color: #4a6cf7;
        }

        .stat-card.active {
            border-color: #2ecc71;
        }

        .stat-card.completed {
            border-color: #9b59b6;
        }

        .stat-card.lessons {
            border-color: #f39c12;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 26px;
            color: white;
        }

        .total .stat-icon {
            background: #4a6cf7;
        }

        .active .stat-icon {
            background: #2ecc71;
        }

        .completed .stat-icon {
            background: #9b59b6;
        }

        .lessons .stat-icon {
            background: #f39c12;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 32px;
            color: #2c3e50;
            font-weight: 700;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .table-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h3 {
            color: #2c3e50;
            font-size: 18px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .students-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #eee;
        }

        .students-table tr:hover {
            background: #f8f9fa;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #4a6cf7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
        }

        .student-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
            font-size: 16px;
        }

        .student-email {
            color: #6c757d;
            font-size: 14px;
        }

        .progress-container {
            width: 100%;
            background: #e9ecef;
            border-radius: 10px;
            height: 10px;
            margin: 8px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #4a6cf7, #6a11cb);
        }

        .progress-text {
            font-size: 14px;
            color: #495057;
            font-weight: 600;
        }

        .completed-lessons {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 4px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .enrolled-date {
            color: #7f8c8d;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #95a5a6;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .empty-state p {
            color: #bdc3c7;
            margin-bottom: 25px;
            font-size: 15px;
        }

        .course-thumbnail {
            width: 80px;
            height: 45px;
            border-radius: 6px;
            background: #f0f0f0;
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

        .course-thumbnail i {
            color: #95a5a6;
            font-size: 20px;
        }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }

        .page-link {
            padding: 8px 16px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            color: #4a6cf7;
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover {
            background: #4a6cf7;
            color: white;
            border-color: #4a6cf7;
        }

        .page-link.active {
            background: #4a6cf7;
            color: white;
            border-color: #4a6cf7;
        }

        .page-link.disabled {
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .page-info {
            margin: 0 15px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas fa-users"></i> Quản lý Học viên</h1>
                <div class="header-info">
                    <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                    <p><i class="fas fa-info-circle"></i> Quản lý học viên đã đăng ký và theo dõi tiến độ học tập</p>
                </div>
            </div>
            <div>
                <a href="?c=course&a=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

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

        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_students ?? 0; ?></h3>
                    <p>Tổng học viên</p>
                </div>
            </div>

            <div class="stat-card active">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $active_students ?? 0; ?></h3>
                    <p>Đang học tích cực</p>
                </div>
            </div>

            <div class="stat-card completed">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $completed_students ?? 0; ?></h3>
                    <p>Đã hoàn thành</p>
                </div>
            </div>

            <div class="stat-card lessons">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_lessons ?? 0; ?></h3>
                    <p>Tổng bài học</p>
                </div>
            </div>
        </div>

        <div class="table-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-list"></i> Danh sách học viên
                    <span class="count-badge"><?php echo $total_students ?? 0; ?> học viên</span>
                </h3>
                <a href="?c=student&a=export&course_id=<?php echo $course['id']; ?>" class="btn btn-success">
                    <i class="fas fa-file-export"></i> Xuất Excel
                </a>
            </div>

            <?php if (empty($students)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h4>Chưa có học viên nào</h4>
                    <p>Chưa có học viên nào đăng ký khóa học này.</p>
                    <p>Chia sẻ khóa học để thu hút học viên!</p>
                </div>
            <?php else: ?>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th width="35%">HỌC VIÊN</th>
                            <th width="25%">TIẾN ĐỘ</th>
                            <th width="20%">TRẠNG THÁI HỌC TẬP</th>
                            <th width="20%">NGÀY ĐĂNG KÝ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <?php
                            // Lấy chữ cái đầu tiên cho avatar
                            $first_letter = strtoupper(substr($student['display_name'] ?? '?', 0, 1));

                            // Sử dụng các biến đã được tính trong controller
                            $progress = (int) ($student['progress'] ?? 0);
                            $completed_lessons = $student['completed_lessons'] ?? 0;
                            $learning_status_text = $student['learning_status_text'] ?? 'Chưa bắt đầu';
                            $learning_status = $student['learning_status'] ?? 'inactive';

                            // Xác định class CSS cho trạng thái học tập
                            $status_class = 'status-' . $learning_status;

                            // Format ngày đăng ký
                            $enrolled_date = $student['enrolled_date_formatted'] ?? 'N/A';
                            ?>
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?php echo $first_letter; ?>
                                        </div>
                                        <div>
                                            <div class="student-name">
                                                <?php echo htmlspecialchars($student['display_name']); ?>
                                            </div>
                                            <div class="student-email">
                                                <?php echo htmlspecialchars($student['display_email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress-text">
                                        <?php echo $progress; ?>%
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-bar" style="width: <?php echo min($progress, 100); ?>%"></div>
                                    </div>
                                    <div class="completed-lessons">
                                        <?php echo $completed_lessons; ?>/<?php echo $total_lessons ?? 0; ?> bài học
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $learning_status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="enrolled-date">
                                        <?php echo $enrolled_date; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- PHÂN TRANG -->
                <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <div class="pagination">
                        <?php if (isset($current_page) && $current_page > 1): ?>
                            <a href="?c=student&a=index&course_id=<?php echo $course['id']; ?>&page=<?php echo $current_page - 1; ?>"
                                class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="page-link disabled">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        <?php endif; ?>

                        <span class="page-info">
                            Trang <?php echo $current_page ?? 1; ?> / <?php echo $total_pages ?? 1; ?>
                        </span>

                        <?php if (isset($current_page) && $current_page < $total_pages): ?>
                            <a href="?c=student&a=index&course_id=<?php echo $course['id']; ?>&page=<?php echo $current_page + 1; ?>"
                                class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="page-link disabled">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Course info card -->
        <div class="table-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> Thông tin khóa học</h3>
            </div>
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div class="course-thumbnail">
                    <?php if (!empty($course['image'])): ?>
                        <img src="assets/uploads/courses/<?php echo $course['image']; ?>"
                            alt="<?php echo htmlspecialchars($course['title']); ?>">
                    <?php else: ?>
                        <i class="fas fa-book"></i>
                    <?php endif; ?>
                </div>
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 10px; color: #2c3e50;"><?php echo htmlspecialchars($course['title']); ?></h4>
                    <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                        <?php if (!empty($course['category'])): ?>
                            <span style="background: #e3f2fd; color: #1976d2; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                <?php echo htmlspecialchars($course['category']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($course['level'])): ?>
                            <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                <?php echo htmlspecialchars($course['level']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (isset($course['price'])): ?>
                            <span style="background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                <?php echo $course['price'] > 0 ? number_format($course['price'], 0, ',', '.') . ' đ' : 'Miễn phí'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($course['description'])): ?>
                        <p style="color: #6c757d; font-size: 14px; margin: 0;">
                            <?php echo htmlspecialchars(substr($course['description'], 0, 200)); ?>...
                        </p>
                    <?php endif; ?>
                </div>
            </div>
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
    </script>
</body>

</html>