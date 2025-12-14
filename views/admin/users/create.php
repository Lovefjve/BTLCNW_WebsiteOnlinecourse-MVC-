<?php
// Trang tạo người dùng mới cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Tài khoản - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        .form-actions { display:flex; gap:12px; justify-content:space-between; align-items:center; margin-top:18px; }
        .actions-left { display:flex; gap:12px; align-items:center; }
        .btn { padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-block; border:0; cursor:pointer; }
        .btn-back { background:#6c757d; color:white; }
        .btn-back:hover { background:#5a6268; }
        .btn-save { background:linear-gradient(180deg,#007bff,#0056b3); color:white; box-shadow:0 6px 18px rgba(0,123,255,0.14); }
        .btn-save:hover { transform:translateY(-2px); }
        .back-link {
            display: none;
        }
        .db-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Tạo Tài khoản Mới</h2>

            <?php if (!empty($errors['db'])): ?>
                <div class="db-error"><?php echo htmlspecialchars($errors['db']); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập *</label>
                    <input type="text" name="username" id="username" value="<?php echo !empty($data['username']) ? htmlspecialchars($data['username']) : ''; ?>" required>
                    <?php if (!empty($errors['username'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['username']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" value="<?php echo !empty($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu *</label>
                    <input type="password" name="password" id="password" required>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['password']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="fullname">Họ tên *</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo !empty($data['fullname']) ? htmlspecialchars($data['fullname']) : ''; ?>" required>
                    <?php if (!empty($errors['fullname'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['fullname']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role">Vai trò *</label>
                    <select name="role" id="role" required>
                        <option value="0" <?php echo (!empty($data['role']) && $data['role'] == 0) ? 'selected' : ''; ?>>Học viên</option>
                        <option value="1" <?php echo (!empty($data['role']) && $data['role'] == 1) ? 'selected' : ''; ?>>Giảng viên</option>
                        <option value="2" <?php echo (!empty($data['role']) && $data['role'] == 2) ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div class="form-actions">
                    <div class="actions-left">
                        <a href="<?php echo BASE_URL; ?>/admin/users" class="btn btn-back">Hủy</a>
                    </div>
                    <div>
                        <button class="btn btn-save" type="submit">Tạo Tài khoản</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
