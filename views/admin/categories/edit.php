<?php
// Chỉnh sửa danh mục cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Danh mục - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .form-container { max-width:600px; margin:30px auto; padding:25px; background:white; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .form-group { margin-bottom:15px; }
        label { display:block; font-weight:600; margin-bottom:6px; }
        input { width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; }
        .error { color:#dc3545; font-size:13px; margin-top:6px; }
        .btn { padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:600; }
        .btn-primary { background:#007bff; color:white; border:none; }
        .btn-primary:hover { background:#0056b3; }
        .back { margin-right:10px; background:#6c757d; color:white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Chỉnh sửa Danh mục</h2>
            <?php if (!empty($errors['db'])): ?><div class="error"><?php echo $errors['db']; ?></div><?php endif; ?>
            <form method="POST" action="<?php echo BASE_URL; ?>/admin/categories/edit?id=<?php echo $category['id']; ?>">
                <div class="form-group">
                    <label for="name">Tên danh mục</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name'] ?? $category['name']); ?>">
                    <?php if (!empty($errors['name'])): ?><div class="error"><?php echo $errors['name']; ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($data['slug'] ?? $category['slug']); ?>">
                    <?php if (!empty($errors['slug'])): ?><div class="error"><?php echo $errors['slug']; ?></div><?php endif; ?>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; align-items:center;">
                    <a href="<?php echo BASE_URL; ?>/admin/categories" class="btn btn-secondary">← Về danh sách</a>
                    <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
