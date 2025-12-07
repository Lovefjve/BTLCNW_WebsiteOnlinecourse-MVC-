<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>ĐĂNG NHẬP</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if(isset($data['error'])): ?>
                            <div class="alert alert-danger text-center">
                                <?= $data['error'] ?>
                            </div>
                        <?php endif; ?>

                        <form action="/onlinecourse/index.php?url=auth/login" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập / Email</label>
                                <input type="text" class="form-control" id="username" name="username" required placeholder="Nhập username...">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Nhập mật khẩu...">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </div>
                        </form>

                    </div>
                    <div class="card-footer text-center">
                        Chưa có tài khoản? <a href="/onlinecourse/index.php?url=auth/register">Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>