<?php
// views/instructor/materials/upload.php

// Kiểm tra biến cần thiết
if (!isset($course) || !isset($lesson) || !isset($materials)) {
    die("Thiếu thông tin cần thiết để hiển thị trang");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài liệu - <?php echo htmlspecialchars($lesson['title'] ?? 'Bài học'); ?></title>
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

        .lesson-info {
            flex: 1;
        }

        .lesson-info h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 10px 0;
        }

        .lesson-info h3 {
            color: #4a6cf7;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
            padding-left: 34px;
        }

        .course-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 6px;
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
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

        .stat-card.size {
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

        .size .stat-icon {
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

        .materials-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-collapse: collapse;
        }

        .materials-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .materials-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .materials-table tr:hover {
            background: #f8f9fa;
        }

        .file-icon {
            text-align: center;
            width: 60px;
        }

        .file-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            margin: 0 auto;
        }

        .file-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .file-name a {
            color: #2c3e50;
            text-decoration: none;
            flex: 1;
        }

        .file-name a:hover {
            color: #4a6cf7;
            text-decoration: underline;
        }

        .file-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .file-info {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #6c757d;
        }

        .file-info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-missing {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f8d7da;
            color: #721c24;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }

        .download-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }

        .upload-date {
            color: #7f8c8d;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
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

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .btn-view:hover {
            background: #138496;
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #218838;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .upload-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .upload-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .upload-area {
            border: 2px dashed #d1d8e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .upload-area:hover {
            border-color: #4a6cf7;
            background: #f0f4ff;
        }

        .upload-icon {
            font-size: 48px;
            color: #4a6cf7;
            margin-bottom: 15px;
        }

        .upload-text h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .upload-text p {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .browse-btn {
            display: inline-block;
            background: #4a6cf7;
            color: white;
            padding: 10px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .browse-btn:hover {
            background: #3a5ce5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.4);
        }

        .file-input {
            display: none;
        }

        .file-info-display {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 14px;
            color: #495057;
        }

        .upload-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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

        .material-count {
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
                <div class="lesson-info">
                    <h1>
                        <i class="fas fa-paperclip"></i> Quản lý Tài liệu
                    </h1>
                    <h3>
                        <i class="fas fa-book-open"></i> <?php echo htmlspecialchars($lesson['title'] ?? 'Bài học'); ?>
                    </h3>
                    <div class="course-badge">
                        <i class="fas fa-graduation-cap"></i>
                        Khóa học: <strong><?php echo htmlspecialchars($course['title'] ?? 'Khóa học'); ?></strong>
                        - Bài học <?php echo htmlspecialchars($lesson['title'] ?? 'Bài học'); ?>
                    </div>
                </div>
                <div class="btn-group">
                    <a href="?c=lesson&a=index&course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
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

        <!-- Upload Section -->
        <div class="upload-section">
            <h3>
                <i class="fas fa-cloud-upload-alt"></i> Tải lên tài liệu mới
            </h3>
            
            <form action="?c=material&a=store" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">
                        <h4>Kéo & Thả file vào đây</h4>
                        <p>hoặc click để chọn file từ máy tính</p>
                    </div>
                    <input type="file" 
                           name="materials[]" 
                           id="fileInput" 
                           class="file-input"
                           multiple 
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.jpg,.jpeg,.png,.gif">
                    <button type="button" class="browse-btn" onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-folder-open"></i> Chọn File
                    </button>
                    
                    <div class="file-info-display" id="fileInfoDisplay" style="display: none;"></div>
                </div>
                
                <div class="upload-actions">
                    
                    
                    <!-- Link hủy dẫn về trang bài học -->
                    
                    <a href="?c=lesson&a=index&course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                   
                    
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-upload"></i> Upload Tài liệu
                    </button>
                </div>
                
                <div style="margin-top: 15px; font-size: 13px; color: #6c757d;">
                    <i class="fas fa-info-circle"></i> 
                    Hỗ trợ: PDF, DOC, PPT, XLS, ZIP, JPG, PNG, TXT (tối đa 50MB/file)
                </div>
            </form>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_materials ?? 0; ?></h3>
                    <p>Tổng tài liệu</p>
                </div>
            </div>
            
            <?php 
            // Tính tổng dung lượng
            $total_size = 0;
            if (isset($materials) && is_array($materials)) {
                foreach ($materials as $material) {
                    if (isset($material['file_path']) && file_exists($material['file_path'])) {
                        $total_size += filesize($material['file_path']);
                    }
                }
            }
            
            // Hàm format size
            function formatFileSize($bytes) {
                if ($bytes === 0) return '0 Bytes';
                $k = 1024;
                $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                $i = floor(log($bytes) / log($k));
                return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
            }
            ?>
            
            <div class="stat-card size">
                <div class="stat-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo formatFileSize($total_size); ?></h3>
                    <p>Tổng dung lượng</p>
                </div>
            </div>
        </div>

        <!-- Table Actions -->
        <div class="table-actions">
            <h3>
                <i class="fas fa-list"></i> Danh sách tài liệu
                <span class="material-count"><?php echo $total_materials ?? 0; ?> tài liệu</span>
            </h3>
        </div>

        <!-- Materials List -->
        <?php if (empty($materials)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h4>Chưa có tài liệu nào</h4>
                <p>Chưa có tài liệu nào được upload cho bài học này.</p>
                <p>Hãy tải lên tài liệu đầu tiên để học viên có thể học tập.</p>
            </div>
        <?php else: ?>
            <table class="materials-table">
                <thead>
                    <tr>
                        <th width="8%"></th>
                        <th width="52%">TÀI LIỆU</th>
                        <th width="25%">NGÀY UPLOAD</th>
                        <th width="15%">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    require_once 'models/Material.php';
                    $materialModel = new Material();
                    
                    foreach ($materials as $material): 
                        $file_icon = $materialModel->getFileIcon($material['file_type'] ?? 'other');
                        $file_color = $materialModel->getFileColor($material['file_type'] ?? 'other');
                        
                        $file_exists = isset($material['file_path']) && file_exists($material['file_path']);
                        $file_size = $file_exists ? $materialModel->formatFileSize(filesize($material['file_path'])) : 'File bị mất';
                        
                        $upload_date = !empty($material['uploaded_at']) ? 
                            date('d/m/Y H:i', strtotime($material['uploaded_at'])) : 
                            'Chưa có';
                    ?>
                        <tr>
                            <td class="file-icon">
                                <div class="file-icon-wrapper" style="background: <?php echo $file_color; ?>;">
                                    <i class="<?php echo $file_icon; ?>"></i>
                                </div>
                            </td>
                            <td>
                                <div class="file-name">
                                    <a href="<?php echo $file_exists ? $material['file_path'] : '#'; ?>" 
                                       target="_blank" 
                                       <?php if(!$file_exists): ?>style="color: #6c757d; cursor: not-allowed;"<?php endif; ?>>
                                        <?php echo htmlspecialchars($material['filename'] ?? 'Không có tên'); ?>
                                    </a>
                                    
                                    <span class="file-type-badge" style="background: <?php echo $file_color; ?>20; color: <?php echo $file_color; ?>;">
                                        <?php echo strtoupper($material['file_type'] ?? 'other'); ?>
                                    </span>
                                    
                                    <?php if (!$file_exists): ?>
                                        <span class="file-missing">
                                            <i class="fas fa-exclamation-triangle"></i> File bị mất
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($material['download_count']) && $material['download_count'] > 0): ?>
                                        <span class="download-count">
                                            <i class="fas fa-download"></i> <?php echo $material['download_count']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="file-info">
                                    <div class="file-info-item">
                                        <i class="fas fa-hdd"></i>
                                        <span><?php echo $file_size; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="upload-date">
                                    <?php echo $upload_date; ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($file_exists): ?>
                                        <!-- Nút Xem trước -->
                                        <a href="<?php echo $material['file_path']; ?>" 
                                           target="_blank" 
                                           class="btn-action btn-view" 
                                           title="Xem trước">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Nút Tải xuống -->
                                        <a href="?c=material&a=download&id=<?php echo $material['id']; ?>" 
                                           class="btn-action btn-download" 
                                           title="Tải xuống">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Nút Xóa -->
                                    <form action="?c=material&a=delete" 
                                          method="POST" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?');"
                                          style="display: inline;">
                                        <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
                                        <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Xóa tài liệu">
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
        // File upload handling
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const fileInfoDisplay = document.getElementById('fileInfoDisplay');
            const submitBtn = document.getElementById('submitBtn');
            
            // Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                uploadArea.style.borderColor = '#4a6cf7';
                uploadArea.style.background = '#f0f4ff';
            }
            
            function unhighlight() {
                uploadArea.style.borderColor = '#d1d8e0';
                uploadArea.style.background = '#f8f9fa';
            }
            
            // Handle drop
            uploadArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }
            
            // Handle file input change
            fileInput.addEventListener('change', function(e) {
                handleFiles(this.files);
            });
            
            // Handle selected files
            function handleFiles(files) {
                if (files.length > 0) {
                    let html = '<strong>Đã chọn ' + files.length + ' file:</strong><br>';
                    
                    for (let i = 0; i < Math.min(files.length, 5); i++) {
                        const file = files[i];
                        const fileName = file.name;
                        
                        html += '<i class="fas fa-file"></i> ' + fileName + '<br>';
                    }
                    
                    if (files.length > 5) {
                        html += '... và ' + (files.length - 5) + ' file khác';
                    }
                    
                    fileInfoDisplay.innerHTML = html;
                    fileInfoDisplay.style.display = 'block';
                    
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload ' + files.length + ' file';
                } else {
                    fileInfoDisplay.style.display = 'none';
                    submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Tài liệu';
                }
            }
            
            // Form validation
            document.getElementById('uploadForm').addEventListener('submit', function(e) {
                const files = fileInput.files;
                
                if (files.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một file để upload!');
                    return;
                }
                
                // Validate file size and type
                const maxSize = 50 * 1024 * 1024; // 50MB
                const allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                
                for (let file of files) {
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    
                    if (file.size > maxSize) {
                        e.preventDefault();
                        alert('File "' + file.name + '" vượt quá kích thước 50MB cho phép!');
                        return;
                    }
                    
                    if (!allowedTypes.includes(fileExt)) {
                        e.preventDefault();
                        alert('File "' + file.name + '" có định dạng không được hỗ trợ!');
                        return;
                    }
                }
                
                // Show loading
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang upload...';
                submitBtn.disabled = true;
            });
        });
        
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