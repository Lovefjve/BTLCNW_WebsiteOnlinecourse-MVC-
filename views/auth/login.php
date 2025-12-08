<?php require_once __DIR__ . "/../layouts/header.php"; ?>

<div class="auth-container">
    <h2>Đăng nhập</h2>

    <form action="/auth/login" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mật khẩu</label>
        <input type="password" name="password" required>

        <button type="submit">Đăng nhập</button>
    </form>
</div>

<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
