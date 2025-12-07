<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Đăng nhập</h2>
        <?php if(isset($_GET['register']) && $_GET['register']=='success'): ?>
            <div class="success">Đăng ký thành công! Vui lòng đăng nhập.</div>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/auth/postLogin" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" name="username" id="username" required>
                <?php if(!empty($errors['username'])): ?>
                    <span class="error"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" required>
                <?php if(!empty($errors['password'])): ?>
                    <span class="error"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            <?php if(!empty($errors['login'])): ?>
                <div class="error"><?php echo $errors['login']; ?></div>
            <?php endif; ?>
            <button type="submit">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/register">Đăng ký</a></p>
    </div>
</body>
</html>