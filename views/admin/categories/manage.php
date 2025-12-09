<?php
// Quản lý danh mục cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .header { display:flex; justify-content:space-between; align-items:center; gap:10px; margin:20px 0; }
        h1 { color:#333; }
        .btn { padding:8px 14px; border-radius:6px; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:8px; }
        .btn-create { background:#007bff; color:white; }
        .btn-create:hover { background:#0056b3; }
        /* Top action buttons */
        .top-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:10px; }
        .btn-secondary { background:#6c757d; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; }
        .btn-secondary:hover { background:#5a6268; }
        .btn-logout { background:#dc3545; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; }
        .btn-logout:hover { background:#c82333; }
        table { width:100%; border-collapse:collapse; margin-top:10px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        th { background:linear-gradient(135deg,#007bff,#0056b3); color:white; padding:12px; text-align:left; }
        td { padding:12px; border-bottom:1px solid #eee; color:#444; }
        tr:hover { background:#f8f9fa; }
        .actions a, .actions form { display:inline-block; margin-right:6px; }
        .btn-edit { background:#ffc107; color:#222; padding:6px 10px; border-radius:6px; text-decoration:none; }
        .btn-delete { background:#dc3545; color:white; padding:6px 10px; border-radius:6px; border:none; cursor:pointer; }
        .success { background:#d4edda; color:#155724; padding:10px; border-radius:6px; margin:10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quản lý Danh mục</h1>
            <a href="<?php echo BASE_URL; ?>/admin/categories/create" class="btn btn-create">+ Tạo Danh mục</a>
        </div>

        <div class="top-actions">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn btn-secondary">← Về Dashboard</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-logout">🚪 Đăng xuất</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                <?php if ($_GET['success'] == 'created') echo 'Tạo danh mục thành công.'; ?>
                <?php if ($_GET['success'] == 'updated') echo 'Cập nhật danh mục thành công.'; ?>
                <?php if ($_GET['success'] == 'deleted') echo 'Xóa danh mục thành công.'; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($categories)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Slug</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['id']); ?></td>
                            <td><?php echo htmlspecialchars($c['name']); ?></td>
                            <td><?php echo htmlspecialchars($c['slug']); ?></td>
                            <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                            <td class="actions">
                                <a class="btn-edit" href="<?php echo BASE_URL; ?>/admin/categories/edit?id=<?php echo $c['id']; ?>">Sửa</a>
                                <form action="<?php echo BASE_URL; ?>/admin/categories/delete" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button class="btn-delete" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Chưa có danh mục nào.</p>
        <?php endif; ?>
    </div>
</body>
</html>
