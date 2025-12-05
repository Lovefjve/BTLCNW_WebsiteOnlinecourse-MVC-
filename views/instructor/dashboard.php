<?php
$title = "Dashboard Giảng Viên";
ob_start();
?>

<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="quickActions" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bolt me-2"></i>Hành Động Nhanh
            </button>
            <ul class="dropdown-menu" aria-labelledby="quickActions">
                <li><a class="dropdown-item" href="/instructor/courses/create">
                    <i class="fas fa-plus-circle me-2"></i>Tạo Khóa Học Mới
                </a></li>
                <li><a class="dropdown-item" href="/instructor/students">
                    <i class="fas fa-users me-2"></i>Xem Học Viên
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Courses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Khóa Học
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $totalCourses ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Students Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Học Viên
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $totalStudents ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Progress Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tiến Độ Trung Bình
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?php echo $avgProgress ?? 0; ?>%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?php echo $avgProgress ?? 0; ?>%" 
                                             aria-valuenow="<?php echo $avgProgress ?? 0; ?>" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Courses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Khóa Học Đang Hoạt Động
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $activeCourses ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Courses -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Khóa Học Gần Đây</h6>
                    <a href="/instructor/courses" class="btn btn-sm btn-primary">Xem Tất Cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tên Khóa Học</th>
                                    <th>Danh Mục</th>
                                    <th>Học Viên</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentCourses)): ?>
                                    <?php foreach ($recentCourses as $course): ?>
                                        <tr>
                                            <td>
                                                <a href="/instructor/courses/<?php echo $course['id']; ?>" 
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($course['title']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($course['category_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php echo $course['student_count'] ?? 0; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($course['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/instructor/courses/<?php echo $course['id']; ?>" 
                                                       class="btn btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/instructor/courses/<?php echo $course['id']; ?>/edit" 
                                                       class="btn btn-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                                            <p class="text-muted">Bạn chưa có khóa học nào</p>
                                            <a href="/instructor/courses/create" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tạo Khóa Học Đầu Tiên
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống Kê Nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-video me-2 text-primary"></i>
                                <span>Bài học đã tạo</span>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <?php echo $totalLessons ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf me-2 text-success"></i>
                                <span>Tài liệu đã upload</span>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                <?php echo $totalMaterials ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle me-2 text-info"></i>
                                <span>Học viên hoàn thành</span>
                            </div>
                            <span class="badge bg-info rounded-pill">
                                <?php echo $completedStudents ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-star me-2 text-warning"></i>
                                <span>Đánh giá trung bình</span>
                            </div>
                            <span class="badge bg-warning rounded-pill">
                                <?php echo $avgRating ?? 0; ?>/5
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liên Kết Nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="/instructor/courses/create" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus me-1"></i>Tạo Khóa Học
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/instructor/students" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-users me-1"></i>Học Viên
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/instructor/my_courses" class="btn btn-info w-100">
                                <i class="fas fa-graduation-cap me-1"></i>Khóa Học Của Tôi
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/courses" class="btn btn-secondary w-100">
                                <i class="fas fa-globe me-1"></i>Trang Chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/header.php';
include 'views/layouts/sidebar.php';
include 'views/layouts/footer.php';
?>