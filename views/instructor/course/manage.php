<?php
// views/instructor/courses/manage.php
session_start();
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Course.php';
require_once __DIR__ . '/../../../models/Enrollment.php';

// Kiểm tra quyền
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header('Location: /onlinecourse/login.php');
//     exit;
// }

$instructor_id = $_SESSION['user_id'];
$courseModel = new Course();
// $enrollmentModel = new Enrollment();

// Lấy tham số
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Lấy dữ liệu
$courses = $courseModel->getByInstructor($instructor_id, $page, $limit);
$totalCourses = $courseModel->countByInstructor($instructor_id);
$totalPages = ceil($totalCourses / $limit);
$stats = $courseModel->getInstructorStats($instructor_id);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khóa Học - Giảng viên</title>
    <link rel="stylesheet" href="/onlinecourse/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .instructor-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #4a6cf7;
        }
        
        .dashboard-header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
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
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
        }
        
        .create-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
            text-decoration: none;
            color: white;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-top: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card.total { border-color: #4a6cf7; }
        .stat-card.published { border-color: #2ecc71; }
        .stat-card.pending { border-color: #f39c12; }
        .stat-card.students { border-color: #9b59b6; }
        
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
        
        .total .stat-icon { background: #4a6cf7; }
        .published .stat-icon { background: #2ecc71; }
        .pending .stat-icon { background: #f39c12; }
        .students .stat-icon { background: #9b59b6; }
        
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
            font-weight: 500;
        }
        
        .courses-section {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .course-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .courses-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .courses-table thead {
            background: #f8f9fa;
        }
        
        .courses-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .courses-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .courses-table tr:hover {
            background: #f8f9fa;
        }
        
        .course-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .course-image {
            width: 80px;
            height: 45px;
            border-radius: 6px;
            object-fit: cover;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }
        
        .course-details h4 {
            margin: 0 0 5px;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .course-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .course-tag {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 500;
        }
        
        .tag-category { background: #e3f2fd; color: #1976d2; }
        .tag-level { background: #e8f5e9; color: #2e7d32; }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-published { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-draft { background: #d1ecf1; color: #0c5460; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        
        .student-count {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .price {
            font-weight: 600;
            color: #2ecc71;
        }
        
        .price.free {
            color: #7f8c8d;
        }
        
        .date {
            color: #7f8c8d;
            font-size: 14px;
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
            font-size: 14px;
        }
        
        .btn-edit { background: #17a2b8; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-view { background: #6c757d; color: white; }
        .btn-students { background: #28a745; color: white; }
        
        .btn-action:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #e0e0e0;
        }
        
        .empty-state h4 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #95a5a6;
        }
        
        .empty-state p {
            margin-bottom: 25px;
            color: #bdc3c7;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .page-link {
            padding: 10px 16px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-decoration: none;
            color: #4a6cf7;
            font-weight: 500;
            transition: all 0.2s;
            min-width: 40px;
            text-align: center;
        }
        
        .page-link:hover {
            background: #e3f2fd;
            border-color: #4a6cf7;
        }
        
        .page-link.active {
            background: #4a6cf7;
            color: white;
            border-color: #4a6cf7;
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
        
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .course-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .courses-table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="instructor-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-chalkboard-teacher"></i> Quản lý Khóa Học</h1>
            <a href="/onlinecourse/instructor/course/create" class="create-btn">
                <i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới
            </a>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
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
                    <a href="./create" class="create-btn" style="margin-top: 15px;">
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
                            $studentCount = $enrollmentModel->countByCourse($course['id']);
                        ?>
                        <tr>
                            <td>
                                <div class="course-info">
                                    <div class="course-image">
                                        <?php if ($course['image']): ?>
                                            <img src="/onlinecourse/assets/uploads/courses/<?php echo htmlspecialchars($course['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fas fa-book"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="course-details">
                                        <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                                        <div class="course-meta">
                                            <?php if ($course['category_name']): ?>
                                            <span class="course-tag tag-category"><?php echo htmlspecialchars($course['category_name']); ?></span>
                                            <?php endif; ?>
                                            <span class="course-tag tag-level"><?php echo htmlspecialchars($course['level']); ?></span>
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
                                ?>
                                <span class="status-badge <?php echo $status_classes[$course['status']] ?? 'status-draft'; ?>">
                                    <?php echo $status_text[$course['status']] ?? 'Bản nháp'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="student-count"><?php echo $studentCount; ?></span>
                            </td>
                            <td>
                                <?php if ($course['price'] > 0): ?>
                                <span class="price"><?php echo number_format($course['price'], 0, ',', '.'); ?> đ</span>
                                <?php else: ?>
                                <span class="price free">Miễn phí</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="date"><?php echo date('d/m/Y', strtotime($course['created_at'])); ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/onlinecourse/instructor/courses/<?php echo $course['id']; ?>/edit" 
                                       class="btn-action btn-edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="/onlinecourse/courses/<?php echo $course['id']; ?>" 
                                       target="_blank" 
                                       class="btn-action btn-view" title="Xem trước">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="/onlinecourse/instructor/courses/<?php echo $course['id']; ?>/students" 
                                       class="btn-action btn-students" title="Học viên">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    
                                    <form action="/onlinecourse/instructor/courses/manage" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này?');">
                                        <input type="hidden" name="_method" value="DELETE">
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
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="page-link">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $page - 2);
            $end = min($totalPages, $start + 4);
            $start = max(1, min($start, $end - 4));
            
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="?page=<?php echo $i; ?>" 
               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="page-link">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Xác nhận xóa
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc muốn xóa khóa học này?\nTất cả bài học và tài liệu sẽ bị xóa.')) {
                    e.preventDefault();
                }
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>