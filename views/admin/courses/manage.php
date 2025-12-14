<?php
// Quản lý khóa học - Danh sách duyệt cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khóa học - Admin</title>
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
        table { width:100%; border-collapse:collapse; margin-top:10px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        th { background:linear-gradient(135deg,#007bff,#0056b3); color:white; padding:12px; text-align:left; }
        td { padding:12px; border-bottom:1px solid #eee; color:#444; }
        tr:hover { background:#f8f9fa; }
        .actions a, .actions form { display:inline-block; margin-right:6px; }
        .btn-detail { background:#17a2b8; color:white; padding:6px 10px; border-radius:6px; text-decoration:none; }
        .btn-detail:hover { background:#138496; }
        .btn-approve { background:#28a745; color:white; padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-weight:600; }
        .btn-approve:hover { background:#218838; }
        .btn-reject { background:#dc3545; color:white; padding:6px 10px; border-radius:6px; border:none; cursor:pointer; font-weight:600; }
        .btn-reject:hover { background:#c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quản lý Khóa học</h1>
        </div>

        <div class="top-actions">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">← Về Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-logout">🚪 Đăng xuất</a>
        </div>

        <?php if (!empty($courses)): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:8%">#</th>
                        <th style="width:35%">Tiêu đề</th>
                        <th style="width:20%">Giảng viên</th>
                        <th style="width:20%">Danh mục</th>
                        <th style="width:17%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['id']); ?></td>
                            <td><?php echo htmlspecialchars($c['title']); ?></td>
                            <td><?php echo htmlspecialchars($c['instructor_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($c['category_name'] ?? 'N/A'); ?></td>
                            <td class="actions">
                                <a class="btn-detail" href="<?php echo BASE_URL; ?>/admin/courses/detail?id=<?php echo $c['id']; ?>">Chi tiết</a>
                                <form method="post" action="<?php echo BASE_URL; ?>/admin/courses/approve" style="display:inline;">
                                    <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                                    <button class="btn-approve" type="submit">Duyệt</button>
                                </form>
                                <form method="post" action="<?php echo BASE_URL; ?>/admin/courses/reject" style="display:inline;">
                                    <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                                    <button class="btn-reject" type="submit">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top:20px; color:#666;">Không có khóa học nào chờ duyệt.</p>
        <?php endif; ?>
    </div>
</body>
</html>
