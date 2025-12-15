<?php
// views/instructor/lessons/manage.php
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bài học - <?php echo htmlspecialchars($course['title'] ?? 'Khóa học'); ?></title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Font và style đồng bộ */
        * {
            font-family: 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #4a6cf7;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .course-info {
            flex: 1;
        }

        .course-info h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 10px 0;
        }

        .course-info h3 {
            color: #4a6cf7;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
            padding-left: 34px;
        }

        .btn {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .btn-purple {
            background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-top: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total {
            border-color: #4a6cf7;
        }

        .stat-card.documents {
            border-color: #28a745;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 26px;
            color: white;
        }

        .total .stat-icon {
            background: #4a6cf7;
        }

        .documents .stat-icon {
            background: #28a745;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 32px;
            color: #2c3e50;
            font-weight: 700;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #95a5a6;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .empty-state p {
            color: #bdc3c7;
            margin-bottom: 25px;
            font-size: 15px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .lessons-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-collapse: collapse;
        }

        .lessons-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .lessons-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .lessons-table tr:hover {
            background: #f8f9fa;
        }

        .lesson-order {
            text-align: center;
            font-weight: 700;
            color: #4a6cf7;
            font-size: 18px;
            width: 60px;
        }

        .lesson-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lesson-title a {
            color: #2c3e50;
            text-decoration: none;
            flex: 1;
        }

        .lesson-title a:hover {
            color: #4a6cf7;
            text-decoration: underline;
        }

        .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .lesson-preview {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
            max-height: 42px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lesson-meta {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #6c757d;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .has-video {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .created-date {
            color: #7f8c8d;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-edit {
            background: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background: #138496;
        }

        .btn-upload {
            background: #28a745;
            color: white;
        }

        .btn-upload:hover {
            background: #218838;
        }

        .btn-materials {
            background: #6f42c1;
            color: white;
        }

        .btn-materials:hover {
            background: #5a32a3;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .upload-wrapper {
            position: relative;
            display: inline-block;
        }

        .upload-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .table-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .table-actions h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 18px;
        }

        .lesson-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="course-info">
                    <h1>
                        <i class="fas fa-book-open"></i> Quản lý Bài học
                    </h1>
                    <h3>
                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($course['title'] ?? 'Khóa học'); ?>
                    </h3>
                </div>
                <div class="btn-group">
                    <a href="<?php echo BASE_URL; ?>/instructor/course/manage" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="<?php echo BASE_URL; ?>/instructor/lessons/create?course_id=<?php echo $course['id'] ?? 0; ?>" class="btn">
                        <i class="fas fa-plus-circle"></i> Thêm bài học
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_lessons ?? 0; ?></h3>
                    <p>Tổng bài học</p>
                </div>
            </div>
            
            <?php if (isset($total_documents)): ?>
            <div class="stat-card documents">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_documents ?? 0; ?></h3>
                    <p>Tổng tài liệu</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Table Actions -->
        <div class="table-actions">
            <h3>
                <i class="fas fa-list"></i> Danh sách bài học
                <span class="lesson-count"><?php echo $total_lessons ?? 0; ?> bài học</span>
            </h3>
        </div>

        <!-- Lessons List -->
        <?php if (empty($lessons)): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h4>Chưa có bài học nào</h4>
                <p>Bắt đầu xây dựng khóa học của bạn bằng cách thêm bài học mẫu. Mỗi bài học có thể chứa video, tài liệu và bài tập.</p>
                <a href="<?php echo BASE_URL; ?>/instructor/lessons/create?course_id=<?php echo $course['id'] ?? 0; ?>" class="btn">
                    <i class="fas fa-plus-circle"></i> Thêm Bài Học Đầu Tiên
                </a>
            </div>
        <?php else: ?>
            <table class="lessons-table">
                <thead>
                    <tr>
                        <th width="8%">STT</th>
                        <th width="52%">BÀI HỌC</th>
                        <th width="23%">NGÀY TẠO</th>
                        <th width="17%">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lessons as $index => $lesson): 
                        $doc_count = $lesson['document_count'] ?? 0;
                        $has_video = !empty($lesson['video_url']);
                        $content_preview = strip_tags($lesson['content'] ?? '');
                        $preview_text = strlen($content_preview) > 100 ? 
                            substr($content_preview, 0, 100) . '...' : 
                            ($content_preview ?: 'Chưa có nội dung');
                    ?>
                        <tr>
                            <td class="lesson-order">
                                <?php echo $lesson['order'] ?? ($index + 1); ?>
                            </td>
                            <td>
                                <div class="lesson-title">
                                    <a href="<?php echo BASE_URL; ?>/instructor/lessons/edit?id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($lesson['title'] ?? 'Bài học không có tiêu đề'); ?>
                                    </a>
                                    <?php if ($doc_count > 0): ?>
                                        <span class="doc-badge" title="<?php echo $doc_count; ?> tài liệu">
                                            <i class="fas fa-paperclip"></i> <?php echo $doc_count; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="lesson-preview">
                                    <?php echo htmlspecialchars($preview_text); ?>
                                </div>
                                
                                <div class="lesson-meta">
                                    <?php if ($has_video): ?>
                                        <div class="has-video">
                                            <i class="fas fa-video"></i> Có video
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($doc_count > 0): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-file-alt"></i>
                                            <span><?php echo $doc_count; ?> tài liệu</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="created-date">
                                    <?php 
                                    if (!empty($lesson['created_at'])) {
                                        echo date('d/m/Y', strtotime($lesson['created_at']));
                                    } else {
                                        echo 'Chưa có';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Nút Chỉnh sửa -->
                                    <a href="<?php echo BASE_URL; ?>/instructor/lessons/edit?id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course['id']; ?>"
                                        class="btn-action btn-edit" title="Chỉnh sửa bài học">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    
                                    <!-- Nút Đăng tài liệu (dẫn đến trang upload) -->
                                    <a href="<?php echo BASE_URL; ?>/instructor/materials/upload?lesson_id=<?php echo $lesson['id']; ?>"
                                        class="btn-action btn-upload" 
                                        title="Tải lên tài liệu">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                    
                                    <!-- Nút Xóa -->
                                    <form action="<?php echo BASE_URL; ?>/instructor/lessons/delete"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa bài học này?');"
                                        style="display: inline;">
                                        <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Xóa bài học">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>