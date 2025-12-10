<?php
// views/instructor/students/progress.php

if (!isset($course) || !isset($student) || !isset($student_progress)) {
    die("Thiếu thông tin cần thiết để hiển thị trang");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến độ học tập - <?php echo htmlspecialchars($student['fullname'] ?? $student['username']); ?></title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
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
            border-left: 5px solid #4a6cf7;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .course-info {
            flex: 1;
        }

        .course-info h1 {
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
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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

        /* Student Detail View */
        .student-detail-view {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .student-header {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .student-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 32px;
            flex-shrink: 0;
        }

        .student-details h2 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .student-details p {
            margin: 5px 0;
            opacity: 0.9;
        }

        .progress-overview {
            padding: 30px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .progress-header h3 {
            color: #2c3e50;
            margin: 0;
            font-size: 22px;
        }

        .progress-percentage {
            font-size: 36px;
            font-weight: 700;
            color: #4a6cf7;
        }

        .progress-container {
            width: 100%;
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            margin: 15px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #4a6cf7, #6a11cb);
            transition: width 0.3s ease;
        }

        .progress-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }

        .lessons-progress {
            padding: 0 30px 30px;
        }

        .lessons-progress h3 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 22px;
        }

        .lesson-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #dee2e6;
        }

        .lesson-item.completed {
            border-left-color: #28a745;
        }

        .lesson-check {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .lesson-item.completed .lesson-check {
            background: #28a745;
            color: white;
        }

        .lesson-content {
            flex: 1;
        }

        .lesson-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="course-info">
                    <h1>
                        <i class="fas fa-user-graduate"></i> Tiến độ học tập
                    </h1>
                    <h3>
                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($course['title']); ?>
                    </h3>
                    <div style="color: #6c757d; font-size: 14px; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> 
                        Xem chi tiết tiến độ học tập của học viên
                    </div>
                </div>
                <div>
                    <a href="?c=student&a=index&course_id=<?php echo $course['id']; ?>" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
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

        <!-- Student Detail View -->
        <div class="student-detail-view">
            <div class="student-header">
                <div class="student-avatar-large">
                    <?php 
                        $first_letter = strtoupper(substr($student['fullname'] ?? $student['username'] ?? '?', 0, 1));
                        echo $first_letter;
                    ?>
                </div>
                <div class="student-details">
                    <h2><?php echo htmlspecialchars($student['fullname'] ?? $student['username']); ?></h2>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($student['email']); ?></p>
                    <p><i class="fas fa-calendar-alt"></i> Đăng ký: 
                        <?php echo date('d/m/Y', strtotime($student_progress['enrolled_at'] ?? date('Y-m-d'))); ?>
                    </p>
                </div>
            </div>

            <div class="progress-overview">
                <div class="progress-header">
                    <h3><i class="fas fa-chart-line"></i> Tổng quan tiến độ học tập</h3>
                    <div class="progress-percentage">
                        <?php echo number_format($student_progress['progress'] ?? 0, 0); ?>%
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?php echo $student_progress['progress'] ?? 0; ?>%"></div>
                </div>
                
                <div class="progress-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $student_progress['completed_lessons'] ?? 0; ?></div>
                        <div class="stat-label">Bài học đã hoàn thành</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $total_lessons; ?></div>
                        <div class="stat-label">Tổng số bài học</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">
                            <?php 
                                if (!empty($student_progress['last_accessed'])) {
                                    echo date('d/m/Y', strtotime($student_progress['last_accessed']));
                                } else {
                                    echo 'Chưa có';
                                }
                            ?>
                        </div>
                        <div class="stat-label">Truy cập gần nhất</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">
                            <?php 
                                $completion_rate = $total_lessons > 0 ? 
                                    ($completed_lessons / $total_lessons * 100) : 0;
                                echo number_format($completion_rate, 0) . '%';
                            ?>
                        </div>
                        <div class="stat-label">Tỷ lệ hoàn thành</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($lessons)): ?>
            <div class="lessons-progress">
                <h3><i class="fas fa-book-open"></i> Tiến độ từng bài học</h3>
                <?php foreach ($lessons as $index => $lesson): ?>
                    <div class="lesson-item <?php echo $lesson['completed'] ? 'completed' : ''; ?>">
                        <div class="lesson-check">
                            <?php if ($lesson['completed']): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <i class="fas fa-clock"></i>
                            <?php endif; ?>
                        </div>
                        <div class="lesson-content">
                            <div class="lesson-title">
                                Bài <?php echo $index + 1; ?>: <?php echo htmlspecialchars($lesson['title']); ?>
                            </div>
                            <div style="font-size: 14px; color: #6c757d;">
                                <?php if (!empty($lesson['description'])): ?>
                                    <div><?php echo htmlspecialchars(substr($lesson['description'], 0, 100)); ?>...</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>