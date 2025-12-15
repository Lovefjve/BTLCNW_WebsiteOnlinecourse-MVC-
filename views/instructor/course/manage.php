<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khóa Học</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles for better appearance */
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
            border-left: 5px solid #4a6cf7;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .header-left {
            flex: 1;
        }

        .header-left h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 10px 0;
        }

        .course-info h3 {
            color: #4a6cf7;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
            padding-left: 34px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
            cursor: pointer;
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

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1ba97e 100%);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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

        .published .stat-icon {
            background: #2ecc71;
        }

        .pending .stat-icon {
            background: #f39c12;
        }

        .students .stat-icon {
            background: #9b59b6;
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

        .courses-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .courses-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .courses-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #eee;
        }

        .courses-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #17a2b8;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-view {
            background: #6c757d;
            color: white;
        }

        .btn-students {
            background: #28a745;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
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
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-header {
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .section-header h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-count-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header với nút quay về Dashboard -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-chalkboard-teacher"></i> Quản lý Khóa Học</h1>
                </div>
                <div class="btn-group">
                    <!-- NÚT QUAY VỀ DASHBOARD -->
                    <a href="<?php echo BASE_URL; ?>/instructor/dashboard" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay về Dashboard
                    </a>
                    <!-- NÚT TẠO KHÓA HỌC MỚI -->
                    <a href="<?php echo BASE_URL; ?>/instructor/course/create" class="btn">
                        <i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới
                    </a>
                </div>
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

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
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

        <!-- Courses List Header -->
        <div class="section-header">
            <h3>
                <i class="fas fa-list"></i> Danh sách khóa học của bạn
                <span class="course-count-badge"><?php echo $totalCourses; ?> khóa học</span>
            </h3>
        </div>

        <!-- Courses Table -->
        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h4>Chưa có khóa học nào</h4>
                <p>Bắt đầu bằng cách tạo khóa học đầu tiên của bạn. Mỗi khóa học có thể chứa nhiều bài học, tài liệu và bài tập cho học viên.</p>
                <div class="btn-group" style="justify-content: center;">
                    <a href="<?php echo BASE_URL; ?>/instructor/dashboard" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay về Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/instructor/course/create" class="btn">
                        <i class="fas fa-plus-circle"></i> Tạo Khóa Học Đầu Tiên
                    </a>
                </div>
            </div>
        <?php else: ?>
            <table class="courses-table">
                <thead>
                    <tr>
                        <th width="32%">Khóa học</th>
                        <th width="13%">Trạng thái</th>
                        <th width="10%">Học viên</th>
                        <th width="10%">Giá</th>
                        <th width="13%">Thời lượng</th>
                        <th width="10%">Ngày tạo</th>
                        <th width="12%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 80px; height: 45px; border-radius: 6px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <?php if (!empty($course['image'])): ?>
                                            <img src="assets/uploads/courses/<?php echo htmlspecialchars($course['image']); ?>"
                                                alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <i class="fas fa-book" style="color: #95a5a6;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0 0 5px; color: #2c3e50;">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </h4>
                                        <div style="display: flex; gap: 10px;">
                                            <span style="background: #e3f2fd; color: #1976d2; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                <?php echo htmlspecialchars($course['category_name'] ?? 'Chưa phân loại'); ?>
                                            </span>
                                            <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                <?php echo htmlspecialchars($course['level'] ?? 'Cơ bản'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                
                                if ($course['status'] === 'published') {
                                    $status_class = 'status-published';
                                    $status_text = 'Đã xuất bản';
                                } elseif ($course['status'] === 'pending') {
                                    $status_class = 'status-pending';
                                    $status_text = 'Chờ duyệt';
                                } else {
                                    $status_class = 'status-pending';
                                    $status_text = 'Nháp';
                                }
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #2c3e50;">
                                    <?php echo $course['student_count']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($course['price'] > 0): ?>
                                    <span style="font-weight: 600; color: #2ecc71;">
                                        <?php echo number_format($course['price'], 0, ',', '.'); ?> đ
                                    </span>
                                <?php else: ?>
                                    <span style="font-weight: 600; color: #7f8c8d;">Miễn phí</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #2c3e50;">
                                    <?php echo $course['duration_weeks']; ?> tuần
                                </span>
                            </td>
                            <td>
                                <span style="color: #7f8c8d; font-size: 14px;">
                                    <?php echo date('d/m/Y', strtotime($course['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo BASE_URL; ?>/instructor/course/edit?id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>/instructor/lessons/manage?course_id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-view" title="Quản lý bài học">
                                        <i class="fas fa-book-open"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>/instructor/students/list?course_id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-students" title="Quản lý học viên">
                                        <i class="fas fa-users"></i>
                                    </a>

                                    <form action="<?php echo BASE_URL; ?>/instructor/course/delete"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này?');"
                                        style="display: inline;">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Confirm before deleting
        function confirmDelete() {
            return confirm('Bạn có chắc muốn xóa khóa học này?\nTất cả bài học và tài liệu sẽ bị xóa.');
        }
    </script>
</body>
</html>