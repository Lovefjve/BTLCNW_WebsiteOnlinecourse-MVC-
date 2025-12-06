<?php
// views/instructor/courses/manage.php

$root_path = '../../../../';

// Kiểm tra và gán giá trị mặc định cho các biến
$stats = $stats ?? [
    'total_courses' => 0,
    'published_courses' => 0,
    'pending_courses' => 0,
    'total_students' => 0
];

$totalCourses = $totalCourses ?? 0;
$courses = $courses ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$studentCount = 0;

// Base URL cho routing (quan trọng!)
$base_url = ''; // Để trống vì dùng relative URL
// Hoặc nếu dùng XAMPP trong thư mục btl: $base_url = '/btl';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khóa Học - Giảng viên</title>
    
    <!-- SỬA ĐƯỜNG DẪN CSS -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>btl/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Debug để kiểm tra */
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #4a6cf7;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Debug thông tin -->
    <div class="debug-info" style="display: none;">
        Debug: 
        Courses count: <?php echo count($courses); ?> | 
        Stats: <?php echo json_encode($stats); ?>
    </div>
    
    <div class="instructor-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-chalkboard-teacher"></i> Quản lý Khóa Học</h1>
            <!-- SỬA LINK: DÙNG URL ROUTING -->
            <a href="?c=instructor&a=courses/create" class="create-btn">
                <i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới
            </a>
        </div>
        
        <!-- Alert Messages -->
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
        
        <!-- Statistics -->
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
        
        <!-- Courses Table -->
        <div class="courses-section">
            <div class="section-header">
                <h3><i class="fas fa-list"></i> Danh sách khóa học của bạn</h3>
                <span class="course-count"><?php echo $totalCourses; ?> khóa học</span>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h4>Chưa có khóa học nào</h4>
                    <p>Bắt đầu bằng cách tạo khóa học đầu tiên của bạn</p>
                    <!-- SỬA LINK -->
                    <a href="?c=instructor&a=courses/create" class="create-btn" style="margin-top: 15px;">
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
                        <?php foreach ($courses as $course): 
                            $studentCount = $course['student_count'] ?? 0;
                        ?>
                        <tr>
                            <td>
                                <div class="course-info">
                                    <div class="course-image">
                                        <?php if (!empty($course['image'])): ?>
                                            <img src="<?php echo $root_path; ?>assets/uploads/courses/<?php echo htmlspecialchars($course['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($course['title'] ?? ''); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-book"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="course-details">
                                        <h4><?php echo htmlspecialchars($course['title'] ?? 'Không có tiêu đề'); ?></h4>
                                        <div class="course-meta">
                                            <?php if (!empty($course['category_name'])): ?>
                                            <span class="course-tag tag-category"><?php echo htmlspecialchars($course['category_name']); ?></span>
                                            <?php endif; ?>
                                            <span class="course-tag tag-level"><?php echo htmlspecialchars($course['level'] ?? 'Beginner'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $status_classes = [
                                    'published' => 'status-published',
                                    'pending' => 'status-pending',
                                    'draft' => 'status-draft',
                                    'rejected' => 'status-rejected'
                                ];
                                $status_text = [
                                    'published' => 'Đã xuất bản',
                                    'pending' => 'Chờ duyệt',
                                    'draft' => 'Bản nháp',
                                    'rejected' => 'Từ chối'
                                ];
                                $status = $course['status'] ?? 'draft';
                                ?>
                                <span class="status-badge <?php echo $status_classes[$status] ?? 'status-draft'; ?>">
                                    <?php echo $status_text[$status] ?? 'Bản nháp'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="student-count"><?php echo $studentCount; ?></span>
                            </td>
                            <td>
                                <?php if (($course['price'] ?? 0) > 0): ?>
                                <span class="price"><?php echo number_format($course['price'], 0, ',', '.'); ?> đ</span>
                                <?php else: ?>
                                <span class="price free">Miễn phí</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="date">
                                    <?php echo !empty($course['created_at']) ? date('d/m/Y', strtotime($course['created_at'])) : 'N/A'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- SỬA LINK: Dùng URL routing -->
                                    <a href="?c=instructor&a=courses/edit&id=<?php echo $course['id']; ?>" 
                                       class="btn-action btn-edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="<?php echo $root_path; ?>courses/<?php echo $course['id']; ?>" 
                                       target="_blank" 
                                       class="btn-action btn-view" title="Xem trước">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="?c=instructor&a=students&course_id=<?php echo $course['id']; ?>" 
                                       class="btn-action btn-students" title="Học viên">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    
                                    <!-- Form xóa -->
                                    <form action="?c=instructor&a=courses/delete" 
                                          method="POST" 
                                          onsubmit="return confirmDelete();">
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
        
        <!-- Pagination - SỬA LINK -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?c=instructor&a=courses&page=<?php echo $page - 1; ?>" class="page-link">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $page - 2);
            $end = min($totalPages, $start + 4);
            $start = max(1, min($start, $end - 4));
            
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="?c=instructor&a=courses&page=<?php echo $i; ?>" 
               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?c=instructor&a=courses&page=<?php echo $page + 1; ?>" class="page-link">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Xác nhận xóa
        function confirmDelete() {
            return confirm('Bạn có chắc muốn xóa khóa học này?\nTất cả bài học và tài liệu sẽ bị xóa.');
        }
        
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Debug: Log ra console
        console.log('Manage Courses Loaded');
        console.log('Courses count:', <?php echo count($courses); ?>);
    </script>
</body>
</html>