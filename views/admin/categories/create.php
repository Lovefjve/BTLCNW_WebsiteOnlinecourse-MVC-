<?php
// Tạo danh mục cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Danh mục - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        body { background: #f4f6f8; }
        .form-container { max-width:720px; margin:36px auto; padding:28px; background:white; border-radius:10px; box-shadow:0 6px 20px rgba(27,31,35,0.06); }
        .form-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:18px; }
        .form-header h2 { margin:0; font-size:1.25rem; color:#222; }
        .form-group { margin-bottom:16px; }
        label { display:block; font-weight:600; margin-bottom:8px; color:#333; }
        input, textarea { width:100%; padding:12px; border:1px solid #e3e6ea; border-radius:8px; background:#fff; font-size:14px; color:#222; }
        textarea { min-height:110px; resize:vertical; }
        .error { color:#dc3545; font-size:13px; margin-top:6px; }
        .form-actions { display:flex; gap:12px; justify-content:space-between; align-items:center; margin-top:18px; }
        .actions-left { display:flex; gap:12px; align-items:center; }
        .btn { padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-block; border:0; cursor:pointer; }
        .btn-back { background:#6c757d; color:white; }
        .btn-back:hover { background:#5a6268; }
        .btn-save { background:linear-gradient(180deg,#007bff,#0056b3); color:white; box-shadow:0 6px 18px rgba(0,123,255,0.14); }
        .btn-save:hover { transform:translateY(-2px); }
        .helper { color:#6b7280; font-size:13px; }
        @media (max-width:600px) {
            .form-actions { flex-direction:column-reverse; align-items:stretch; }
            .actions-left { justify-content:flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Tạo Danh mục Mới</h2>
            <?php if (!empty($errors['db'])): ?><div class="error"><?php echo $errors['db']; ?></div><?php endif; ?>
            <form method="POST" action="<?php echo BASE_URL; ?>/admin/categories/create">
                <div class="form-group">
                    <label for="name">Tên danh mục</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>">
                    <?php if (!empty($errors['name'])): ?><div class="error"><?php echo $errors['name']; ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả (tùy chọn)</label>
                    <textarea id="description" name="description" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                    <?php if (!empty($errors['description'])): ?><div class="error"><?php echo $errors['description']; ?></div><?php endif; ?>
                </div>

                <div class="form-actions">
                    <div class="actions-left">
                        <a href="<?php echo BASE_URL; ?>/admin/categories" class="btn btn-back">Hủy</a>
                    </div>
                    <div>
                        <button class="btn btn-save" type="submit">Tạo danh mục</button>
                    </div>
                </div>
            </form>
            </form>
        </div>
    </div>
</body>
</html>
