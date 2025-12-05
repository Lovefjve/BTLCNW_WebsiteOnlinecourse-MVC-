<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Online Course'; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            padding-top: 20px;
        }
        
        .sidebar a {
            color: #ecf0f1;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        
        .sidebar a:hover {
            background-color: #34495e;
            border-left: 3px solid #3498db;
        }
        
        .sidebar a.active {
            background-color: #34495e;
            border-left: 3px solid #3498db;
            font-weight: bold;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                Online Course
            </a>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        Giảng Viên
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/instructor/dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/auth/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>" 
                               href="/instructor/dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'courses') !== false) ? 'active' : ''; ?>" 
                               href="/instructor/courses">
                                <i class="fas fa-book me-2"></i>
                                Khóa Học
                            </a>
                        </li>
                        
                        <li class="nav-item mb-2">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'students') !== false) ? 'active' : ''; ?>" 
                               href="/instructor/students">
                                <i class="fas fa-users me-2"></i>
                                Học Viên
                            </a>
                        </li>
                        
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="/instructor/my_courses">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Khóa Học Của Tôi
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">