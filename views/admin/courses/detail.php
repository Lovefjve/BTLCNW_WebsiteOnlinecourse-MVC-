<?php
// Chi tiết khóa học - Admin duyệt
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Khóa học - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .header { display:flex; justify-content:space-between; align-items:center; gap:10px; margin:20px 0; }
        h1 { color:#333; margin:0; }
        .btn { padding:8px 14px; border-radius:6px; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:8px; }
        .btn-secondary { background:#6c757d; color:white; }
        .btn-secondary:hover { background:#5a6268; }
        .btn-logout { background:#dc3545; color:white; }
        .btn-logout:hover { background:#c82333; }
        .top-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:10px; }
        .detail-box { max-width:900px; background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #007bff; }
        .detail-box h2 { color:#333; margin-top:0; }
        .detail-row { margin:12px 0; color:#444; }
        .detail-label { font-weight:600; color:#333; display:inline-block; width:140px; }
        .actions-group { margin-top:20px; }
        .btn-action { padding:8px 14px; border-radius:6px; border:none; cursor:pointer; font-weight:600; margin-right:8px; }
        .btn-approve { background:#28a745; color:white; }
        .btn-approve:hover { background:#218838; }
        .btn-reject { background:#dc3545; color:white; }
        .btn-reject:hover { background:#c82333; }
        .no-action { margin-top:15px; color:#666; font-style:italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chi tiết Khóa học</h1>
        </div>

        <div class="top-actions">
            <a href="<?php echo BASE_URL; ?>/admin/courses" class="btn btn-secondary">Hủy</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-logout">🚪 Đăng xuất</a>
        </div>

        <?php if ($course): ?>
            <div class="detail-box">
                <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                
                <div class="detail-row">
                    <span class="detail-label">Giảng viên:</span>
                    <?php echo htmlspecialchars($course['instructor_name'] ?? 'N/A'); ?>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Danh mục:</span>
                    <?php echo htmlspecialchars($course['category_name'] ?? 'N/A'); ?>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Mô tả:</span><br>
                    <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Giá:</span>
                    <?php echo htmlspecialchars($course['price']); ?> đ
                </div>

                <div class="detail-row">
                    <span class="detail-label">Thời lượng:</span>
                    <?php echo htmlspecialchars($course['duration_weeks']); ?> tuần
                </div>

                <div class="detail-row">
                    <span class="detail-label">Level:</span>
                    <?php echo htmlspecialchars($course['level']); ?>
                </div>

                <?php if ($course['status'] === 'pending'): ?>
                    <div class="actions-group">
                        <form method="post" action="<?php echo BASE_URL; ?>/admin/courses/approve" style="display:inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <button class="btn-action btn-approve" type="submit">✓ Duyệt</button>
                        </form>

                        <form method="post" action="<?php echo BASE_URL; ?>/admin/courses/reject" style="display:inline;">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <button class="btn-action btn-reject" type="submit">✗ Từ chối</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="no-action">Hành động không khả dụng (khóa học đã được xử lý).</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p style="margin-top:20px; color:#666;">Khóa học không tồn tại.</p>
        <?php endif; ?>
    </div>
</body>
</html>
