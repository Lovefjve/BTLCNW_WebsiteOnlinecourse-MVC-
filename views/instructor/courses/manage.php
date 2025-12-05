<?php
$title = "Quản Lý Khóa Học";
?>

<?php include '../../layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản Lý Khóa Học</h1>
        <a href="/instructor/courses/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo Khóa Học Mới
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tổng số khóa học</h6>
                            <h3 class="mb-0"><?php echo $totalCourses ?? 0; ?></h3>
                        </div>
                        <div class="bg-primary text-white rounded-circle p-3">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Đã xuất bản</h6>
                            <h3 class="mb-0"><?php echo $publishedCourses ?? 0; ?></h3>
                        </div>
                        <div class="bg-success text-white rounded-circle p-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh sách khóa học của bạn</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên Khóa Học</th>
                                <th>Trạng Thái</th>
                                <th>Học Viên</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $index => $course): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($course['title'] ?? 'Chưa có tiêu đề'); ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($course['is_published'] ?? false): ?>
                                            <span class="badge bg-success">Đã xuất bản</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nháp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo $course['student_count'] ?? 0; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">Chưa có khóa học nào</h4>
                    <p class="text-muted mb-4">Bắt đầu bằng cách tạo khóa học đầu tiên của bạn.</p>
                    <a href="/instructor/courses/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tạo Khóa Học Đầu Tiên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>