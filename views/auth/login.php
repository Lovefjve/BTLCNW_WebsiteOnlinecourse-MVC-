<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
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
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        input:focus {
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
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
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
        p {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
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
    </div>
</body>
</html>