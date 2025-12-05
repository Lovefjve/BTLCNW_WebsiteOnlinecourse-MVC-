<!-- Sidebar for Instructor -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h3 class="text-white">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Giảng Viên
        </h3>
    </div>
    
    <ul class="list-unstyled components">
        <!-- Dashboard -->
        <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>">
            <a href="/instructor/dashboard">
                <i class="fas fa-tachometer-alt me-2"></i>
                Dashboard
            </a>
        </li>
        
        <!-- Courses Dropdown -->
        <li class="dropdown-toggle <?php echo (strpos($_SERVER['REQUEST_URI'], 'courses') !== false) ? 'active' : ''; ?>">
            <a href="#coursesSubmenu" data-bs-toggle="collapse" aria-expanded="false" 
               class="dropdown-toggle">
                <i class="fas fa-book me-2"></i>
                Khóa Học
            </a>
            <ul class="collapse list-unstyled" id="coursesSubmenu">
                <li>
                    <a href="/instructor/courses">
                        <i class="fas fa-list me-2"></i>
                        Quản Lý
                    </a>
                </li>
                <li>
                    <a href="/instructor/courses/create">
                        <i class="fas fa-plus-circle me-2"></i>
                        Tạo Mới
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Students -->
        <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'students') !== false) ? 'active' : ''; ?>">
            <a href="/instructor/students">
                <i class="fas fa-users me-2"></i>
                Học Viên
            </a>
        </li>
        
        <!-- My Courses -->
        <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'my_courses') !== false) ? 'active' : ''; ?>">
            <a href="/instructor/my_courses">
                <i class="fas fa-graduation-cap me-2"></i>
                Khóa Học Của Tôi
            </a>
        </li>
        
        <!-- Divider -->
        <li class="sidebar-divider"></li>
        
        <!-- Account -->
        <li>
            <a href="/auth/profile">
                <i class="fas fa-user-circle me-2"></i>
                Tài Khoản
            </a>
        </li>
        <li>
            <a href="/auth/logout">
                <i class="fas fa-sign-out-alt me-2"></i>
                Đăng Xuất
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer mt-auto p-3 text-center">
        <small class="text-white-50">
            © <?php echo date('Y'); ?> Online Course
        </small>
    </div>
</nav>