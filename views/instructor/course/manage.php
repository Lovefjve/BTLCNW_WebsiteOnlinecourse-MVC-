<?php
// views/instructor/course/manage.php

$root_path = '../../';
?>

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
        }

        .empty-state i {
            font-size: 64px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chalkboard-teacher"></i> Quản lý Khóa Học</h1>
            <a href="?c=instructor&a=createCourse" class="btn">
                <i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới
            </a>
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

        <div style="margin-bottom: 20px; padding: 20px; background: white; border-radius: 10px;">
            <h3 style="margin-bottom: 15px; color: #2c3e50;">
                <i class="fas fa-list"></i> Danh sách khóa học của bạn
                <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 20px; font-size: 14px; margin-left: 10px;">
                    <?php echo $totalCourses; ?> khóa học
                </span>
            </h3>
        </div>

        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h4 style="color: #95a5a6; margin-bottom: 10px;">Chưa có khóa học nào</h4>
                <p style="color: #bdc3c7; margin-bottom: 25px;">Bắt đầu bằng cách tạo khóa học đầu tiên của bạn</p>
                <a href="?c=instructor&a=createCourse" class="btn">
                    <i class="fas fa-plus-circle"></i> Tạo Khóa Học Đầu Tiên
                </a>
            </div>
        <?php else: ?>
            <table class="courses-table">
                <thead>
                    <tr>
                        <th width="35%">Khóa học</th>
                        <th width="15%">Trạng thái</th>
                        <th width="10%">Học viên</th>
                        <th width="10%">Giá</th>
                        <th width="15%">Ngày tạo</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 80px; height: 45px; border-radius: 6px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                        <?php if (!empty($course['image'])): ?>
                                            <img src="assets/uploads/courses/<?php echo $course['image']; ?>"
                                                alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                        <?php else: ?>
                                            <i class="fas fa-book" style="color: #95a5a6;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0 0 5px; color: #2c3e50;"><?php echo htmlspecialchars($course['title']); ?></h4>
                                        <div style="display: flex; gap: 10px;">
                                            <span style="background: #e3f2fd; color: #1976d2; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                <?php echo $course['category_name']; ?>
                                            </span>
                                            <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                <?php echo $course['level']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';

                                // Chỉ xử lý published và pending, không xử lý draft nữa
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
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #2c3e50;"><?php echo $course['student_count']; ?></span>
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
                                <span style="color: #7f8c8d; font-size: 14px;">
                                    <?php echo date('d/m/Y', strtotime($course['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?c=instructor&a=edit&id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="#" target="_blank"
                                        class="btn-action btn-view" title="Xem trước">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="?c=instructor&a=students&course_id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-students" title="Học viên">
                                        <i class="fas fa-users"></i>
                                    </a>

                                    <form action="?c=instructor&a=delete"
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