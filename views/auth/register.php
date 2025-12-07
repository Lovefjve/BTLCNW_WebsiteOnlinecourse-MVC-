<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Đăng ký tài khoản</h2>
        <form action="<?php echo BASE_URL; ?>/auth/postRegister" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" name="username" id="username" value="<?php echo !empty($data['username']) ? htmlspecialchars($data['username']) : ''; ?>" required>
                <?php if(!empty($errors['username'])): ?>
                    <span class="error"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo !empty($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
                <?php if(!empty($errors['email'])): ?>
                    <span class="error"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" required>
                <?php if(!empty($errors['password'])): ?>
                    <span class="error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="fullname">Họ tên</label>
                <input type="text" name="fullname" id="fullname" value="<?php echo !empty($data['fullname']) ? htmlspecialchars($data['fullname']) : ''; ?>" required>
                <?php if(!empty($errors['fullname'])): ?>
                    <span class="error"><?php echo $errors['fullname']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="role">Vai trò</label>
                <select name="role" id="role">
                    <option value="0" <?php echo (!empty($data['role']) && $data['role']==0) ? 'selected' : ''; ?>>Học viên</option>
                    <option value="1" <?php echo (!empty($data['role']) && $data['role']==1) ? 'selected' : ''; ?>>Giảng viên</option>
                </select>
                <small class="note">(Chỉ cho phép đăng ký Học viên hoặc Giảng viên)</small>
            </div>
            <?php if(!empty($errors['db'])): ?>
                <div class="error"><?php echo $errors['db']; ?></div>
            <?php endif; ?>
            <button type="submit">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/login">Đăng nhập</a></p>
    </div>
</body>
</html>