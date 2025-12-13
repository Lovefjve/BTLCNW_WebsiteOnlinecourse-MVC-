<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body data-page="register">
  <main class="page-center">
    <h1>Đăng ký</h1>

    <form class="login-container register-container" id="registerForm" action="/register" method="POST" novalidate>
      <label for="name">Họ và tên</label>
      <input id="name" name="name" type="text" required>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Mật khẩu</label>
      <input id="password" name="password" type="password" minlength="6" required>

      <label for="confirm">Xác nhận mật khẩu</label>
      <input id="confirm" name="confirm" type="password" minlength="6" required>

      <label class="checkbox">
        <input type="checkbox" name="terms" required>
        Tôi đồng ý với <a href="#">Điều khoản sử dụng</a>
      </label>

      <button type="submit">Đăng ký</button>

      <p class="form-footer">
        Đã có tài khoản? <a href="./../auth/login.php">Đăng nhập</a>
      </p>
    </form>
  </main>

  <script src="/assets/js/script.js" defer></script>
</body>
</html>