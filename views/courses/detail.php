<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết khóa học</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="display-4 fw-bold mb-4 text-primary">
                <?= htmlspecialchars($course['title']) ?>
            </h1>

            <!-- Ảnh khóa học -->
            <div class="bg-gradient text-white rounded-4 shadow mb-5 
                        d-flex align-items-center justify-content-center" 
                 style="height: 400px; background: linear-gradient(45deg, #667eea, #764ba2); font-size: 8rem;">
                <i class="bi bi-book-half"></i>
            </div>

            <!-- Mô tả -->
            <div class="bg-white p-5 rounded-4 shadow">
                <h3 class="mb-4 text-primary">
                    <i class="bi bi-file-text"></i> Mô tả khóa học
                </h3>
                <p class="lead">
                    <?= nl2br(htmlspecialchars($course['description'] ?? 'Chưa có mô tả chi tiết.')) ?>
                </p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-primary">Thông tin khóa học</h4>
                    
                    <div class="mb-4">
                        <p class="mb-2"><strong>Giảng viên:</strong> <?= htmlspecialchars($course['instructor_name'] ?? 'Chưa có') ?></p>
                        <p class="mb-2"><strong>Danh mục:</strong> <?= htmlspecialchars($course['category_name'] ?? 'Chưa có') ?></p>
                        <p class="mb-2"><strong>Thời lượng:</strong> <?= $course['duration_weeks'] ?? 'Chưa xác định' ?> tuần</p>
                        <p class="mb-3">
                            <strong>Học viên:</strong> 
                            <span class="badge bg-success fs-6"><?= $totalStudents ?></span>
                        </p>
                    </div>

                    <hr>
                    <h2 class="text-danger text-center fw-bold mb-4">
                        <?= number_format($course['price']) ?>đ
                    </h2>
                    <hr>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                        <?php if ($isEnrolled): ?>
                            <button class="btn btn-success btn-lg w-100 rounded-pill shadow" disabled>
                                <i class="bi bi-check-circle-fill"></i> Đã đăng ký
                            </button>
                            <a href="#" class="btn btn-outline-primary w-100 mt-3 rounded-pill">
                                <i class="bi bi-play-circle"></i> Vào học ngay
                            </a>
                        <?php else: ?>
                            <a href="?url=course/enroll/<?= $course['id'] ?>" 
                               class="btn btn-primary btn-lg w-100 rounded-pill shadow">
                                <i class="bi bi-cart-plus"></i> Đăng ký ngay
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-info-circle"></i><br>
                            Đăng nhập tài khoản học viên để đăng ký
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
