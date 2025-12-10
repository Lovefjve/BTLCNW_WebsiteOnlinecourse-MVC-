<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body data-page="login">
  <main class="page-center">
    <h1>Đăng nhập</h1>
    <form class="login-container" id="loginForm" action="/login" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Đăng nhập</button>
    </form>
  </main>

  <script src="main.js" defer></script>
</body>
</html>