
-- Tạo bảng users       (NGƯỜI DÙNG)
    CREATE TABLE users
    (
        id       INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(255) UNIQUE NOT NULL,
        email    VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255)        NOT NULL,
        fullname VARCHAR(255),
        role     INT                 NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

-- Tạo bảng users_cho_duyet (TÀI KHOẢN CHỜ DUYỆT)
    CREATE TABLE users_cho_duyet
    (
        id       INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(255) UNIQUE NOT NULL,
        email    VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255)        NOT NULL,
        fullname VARCHAR(255),
        role     INT                 NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

-- Tạo bảng categories      (DANH MỤC KHÓA HỌC)
    CREATE TABLE categories
    (
        id          INT PRIMARY KEY AUTO_INCREMENT,
        name        VARCHAR(255) NOT NULL,
        description TEXT,
        created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
    );

-- Tạo bảng courses         (KHÓA HỌC)
    CREATE TABLE courses
    (
        id             INT PRIMARY KEY AUTO_INCREMENT,
        title          VARCHAR(255) NOT NULL,
        description    TEXT,
        instructor_id  INT          NOT NULL,
        category_id    INT          NOT NULL,
        price          DECIMAL(10, 2) DEFAULT 0.00,
        duration_weeks INT,
        level          VARCHAR(50),
        image VARCHAR(255),
        created_at     DATETIME       DEFAULT CURRENT_TIMESTAMP,
        updated_at     DATETIME       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (instructor_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
    );

-- Tạo bảng courses  chờ duyệt      (KHÓA HỌC)
    CREATE TABLE courses_cho_duyet
    (
        id             INT PRIMARY KEY AUTO_INCREMENT,
        title          VARCHAR(255) NOT NULL,
        description    TEXT,
        instructor_id  INT          NOT NULL,
        category_id    INT          NOT NULL,
        price          DECIMAL(10, 2) DEFAULT 0.00,
        duration_weeks INT,
        level          VARCHAR(50),
        image VARCHAR(255),
        created_at     DATETIME       DEFAULT CURRENT_TIMESTAMP,
        updated_at     DATETIME       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (instructor_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
    );

-- Tạo bảng enrollments         (ĐĂNG KÝ HỌC)
    CREATE TABLE enrollments
    (
        id            INT PRIMARY KEY AUTO_INCREMENT,
        course_id     INT NOT NULL,
        student_id    INT NOT NULL,
        enrolled_date DATETIME    DEFAULT CURRENT_TIMESTAMP,
        status        VARCHAR(50) DEFAULT 'active',
        progress INT DEFAULT 0,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
        UNIQUE KEY unique_enrollment (course_id, student_id)
    );

-- Tạo bảng lessons         (BÀI HỌC)
    CREATE TABLE lessons
    (
        id         INT PRIMARY KEY AUTO_INCREMENT,
        course_id  INT          NOT NULL,
        title      VARCHAR(255) NOT NULL,
        content    LONGTEXT,
        video_url  VARCHAR(255),
        `order`      INT      DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
    );

-- Tạo bảng materials               (TÀI LIỆU HỌC TẬP)
    CREATE TABLE materials
    (
        id        INT PRIMARY KEY AUTO_INCREMENT,
        lesson_id INT          NOT NULL,
        filename  VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_type VARCHAR(50),
        uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE
    );


-- ========================================
-- THÊM DỮ LIỆU MẪU
-- ========================================

-- Thêm 5 users (2 quản trị viên, 2 giảng viên, 1 học viên sẽ thêm sau)
    INSERT INTO users (username, email, password, fullname, role, created_at) VALUES
    ('admin1', 'admin1@example. com', '$2y$10$abcdefghijklmnopqrstuv', 'Nguyễn Văn Admin', 2, '2024-01-15 08:00:00'),
    ('admin2', 'admin2@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Trần Thị Quản Trị', 2, '2024-01-20 09:30:00'),
    ('gv_nguyen', 'nguyenvana@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Nguyễn Văn A', 1, '2024-02-01 10:00:00'),
    ('gv_tran', 'tranthib@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Trần Thị B', 1, '2024-02-05 11:00:00'),
    ('hv_le', 'levancuong@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Lê Văn Cường', 0, '2024-03-01 14:00:00');

    -- Thêm 4 học viên nữa
    INSERT INTO users (username, email, password, fullname, role, created_at) VALUES
    ('hv_pham', 'phamthid@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Phạm Thị D', 0, '2024-03-05 15:00:00'),
    ('hv_hoang', 'hoangvane@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Hoàng Văn E', 0, '2024-03-10 16:00:00'),
    ('hv_vu', 'vuthif@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Vũ Thị F', 0, '2024-03-15 17:00:00'),
    ('hv_dang', 'dangvang@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Đặng Văn G', 0, '2024-03-20 18:00:00');

-- Thêm 5 categories
    INSERT INTO categories (name, description, created_at) VALUES
    ('Lập trình Web', 'Các khóa học về phát triển website và ứng dụng web', '2024-01-10 08:00:00'),
    ('Lập trình Mobile', 'Khóa học phát triển ứng dụng di động iOS và Android', '2024-01-11 09:00:00'),
    ('Data Science', 'Khóa học về khoa học dữ liệu, machine learning và AI', '2024-01-12 10:00:00'),
    ('DevOps', 'Khóa học về triển khai, quản lý hệ thống và CI/CD', '2024-01-13 11:00:00'),
    ('An ninh mạng', 'Các khóa học về bảo mật thông tin và an toàn mạng', '2024-01-14 12:00:00');

-- Thêm 5 courses
    INSERT INTO courses (title, description, instructor_id, category_id, price, duration_weeks, level, image, created_at)
    VALUES ('HTML CSS từ cơ bản đến nâng cao', 'Học HTML và CSS để xây dựng giao diện web đẹp mắt', 3, 1, 299000.00, 4,'Beginner', 'html-css. jpg', '2024-02-10 10:00:00'),
           ('JavaScript ES6+ cho người mới', 'Nắm vững JavaScript hiện đại với ES6+', 3, 1, 499000.00, 6,'Intermediate', 'javascript. jpg', '2024-02-15 11:00:00'),
           ('React Native - Xây dựng App Mobile', 'Tạo ứng dụng di động đa nền tảng với React Native', 4, 2, 799000.00,8, 'Intermediate', 'react-native.jpg', '2024-02-20 12:00:00'),
           ('Python cho Data Science', 'Phân tích dữ liệu và machine learning với Python', 4, 3, 899000.00, 10,'Advanced', 'python-ds.jpg', '2024-02-25 13:00:00'),
           ('Docker và Kubernetes cơ bản', 'Triển khai ứng dụng với container và orchestration', 3, 4, 699000.00, 6,'Intermediate', 'docker-k8s.jpg', '2024-03-01 14:00:00');

-- Thêm 5 enrollments
    INSERT INTO enrollments (course_id, student_id, enrolled_date, status, progress)
    VALUES (1, 5, '2024-03-05 09:00:00', 'active', 75),
           (2, 5, '2024-03-10 10:00:00', 'active', 45),
           (1, 6, '2024-03-12 11:00:00', 'active', 30),
           (3, 7, '2024-03-15 14:00:00', 'completed', 100),
           (4, 8, '2024-03-18 15:00:00', 'active', 20);

-- Thêm 5 lessons cho khóa học đầu tiên (HTML CSS)
    INSERT INTO lessons (course_id, title, content, video_url, `order`, created_at)
    VALUES (1, 'Giới thiệu về HTML', 'Tìm hiểu cấu trúc cơ bản của HTML và các thẻ quan trọng',
            'https://youtube.com/watch?v=abc123', 1, '2024-02-10 10:30:00'),
           (1, 'CSS cơ bản - Styling', 'Học cách tạo kiểu cho trang web với CSS', 'https://youtube. com/watch?v=def456',
            2, '2024-02-10 11:00:00'),
           (1, 'CSS Flexbox Layout', 'Sử dụng Flexbox để tạo layout linh hoạt', 'https://youtube.com/watch?v=ghi789', 3,
            '2024-02-10 11:30:00'),
           (1, 'CSS Grid System', 'Tạo layout phức tạp với CSS Grid', 'https://youtube.com/watch?v=jkl012', 4,
            '2024-02-10 12:00:00'),
           (1, 'Responsive Design', 'Thiết kế web đáp ứng trên mọi thiết bị', 'https://youtube.com/watch?v=mno345', 5,
            '2024-02-10 12:30:00');

-- Thêm 5 materials cho bài học đầu tiên
    INSERT INTO materials (lesson_id, filename, file_path, file_type, uploaded_at)
    VALUES (1, 'HTML_Cheatsheet.pdf', '/uploads/materials/html-cheatsheet.pdf', 'pdf', '2024-02-10 13:00:00'),
           (1, 'HTML_Tags_Reference.pdf', '/uploads/materials/html-tags. pdf', 'pdf', '2024-02-10 13:15:00'),
           (2, 'CSS_Properties.pdf', '/uploads/materials/css-properties.pdf', 'pdf', '2024-02-10 13:30:00'),
           (2, 'CSS_Examples.zip', '/uploads/materials/css-examples.zip', 'zip', '2024-02-10 13:45:00'),
           (3, 'Flexbox_Guide.pdf', '/uploads/materials/flexbox-guide.pdf', 'pdf', '2024-02-10 14:00:00');