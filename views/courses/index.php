<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách khóa học</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="bg-light">

<div class="container py-4">

    <!-- Form tìm kiếm -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm khóa học..." 
                           value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                <?= ($category ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng khóa học -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Khóa học</th>
                            <th>Giảng viên</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Thời lượng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i><br>
                                    Không tìm thấy khóa học nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($c['title']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($c['instructor_name'] ?? 'Chưa có') ?></td>
                                    <td><?= htmlspecialchars($c['category_name'] ?? 'Chưa có') ?></td>
                                    <td class="text-danger fw-bold"><?= number_format($c['price']) ?>đ</td>
                                    <td><?= $c['duration_weeks'] ?? '?' ?> tuần</td>
                                    <td>
                                        <a href="?url=course/detail/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                                            <a href="?url=course/enroll/<?= $c['id'] ?>" class="btn btn-sm btn-success text-white">
                                                <i class="bi bi-cart-plus"></i> Đăng ký
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
