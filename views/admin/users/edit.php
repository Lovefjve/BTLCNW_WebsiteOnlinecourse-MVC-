<?php
// Trang chỉnh sửa thông tin người dùng cho Admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Tài khoản - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .error {
            color: red;
            font-size: 12px;
            margin-top: 3px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
        }
        .db-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .password-note {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <a href="<?php echo BASE_URL; ?>/admin/users" class="back-link">← Quay lại danh sách</a>
            <h2>Chỉnh sửa Tài khoản</h2>

            <?php if (!empty($errors['db'])): ?>
                <div class="db-error"><?php echo htmlspecialchars($errors['db']); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" name="username" id="username" value="<?php echo !empty($editUser['username']) ? htmlspecialchars($editUser['username']) : ''; ?>" disabled>
                    <small style="color: #666;">Không thể thay đổi tên đăng nhập</small>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" value="<?php echo !empty($data['email']) ? htmlspecialchars($data['email']) : (isset($editUser['email']) ? htmlspecialchars($editUser['email']) : ''); ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password">
                    <div class="password-note">Để trống nếu không muốn thay đổi mật khẩu</div>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['password']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="fullname">Họ tên *</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo !empty($data['fullname']) ? htmlspecialchars($data['fullname']) : (isset($editUser['fullname']) ? htmlspecialchars($editUser['fullname']) : ''); ?>" required>
                    <?php if (!empty($errors['fullname'])): ?>
                        <div class="error"><?php echo htmlspecialchars($errors['fullname']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role">Vai trò *</label>
                    <select name="role" id="role" required>
                        <option value="0" <?php echo (!empty($data['role']) && $data['role'] == 0) ? 'selected' : (isset($editUser['role']) && $editUser['role'] == 0 ? 'selected' : ''); ?>>Học viên</option>
                        <option value="1" <?php echo (!empty($data['role']) && $data['role'] == 1) ? 'selected' : (isset($editUser['role']) && $editUser['role'] == 1 ? 'selected' : ''); ?>>Giảng viên</option>
                        <option value="2" <?php echo (!empty($data['role']) && $data['role'] == 2) ? 'selected' : (isset($editUser['role']) && $editUser['role'] == 2 ? 'selected' : ''); ?>>Admin</option>
                    </select>
                </div>

                <button type="submit">Cập nhật Tài khoản</button>
            </form>
        </div>
    </div>
</body>
</html>
