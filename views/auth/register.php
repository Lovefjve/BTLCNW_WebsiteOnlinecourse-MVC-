<?php require_once __DIR__ . "/../layouts/header.php"; ?>

<div class="auth-container">
    <h2>Đăng ký</h2>

    <form action="/auth/register" method="POST">
        <label>Họ tên</label>
        <input type="text" name="fullname" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mật khẩu</label>
        <input type="password" name="password" required>

        <button type="submit">Tạo tài khoản</button>
    </form>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
