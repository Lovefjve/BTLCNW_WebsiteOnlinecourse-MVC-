<?php
// views/instructor/materials/upload.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Tài liệu - <?php echo htmlspecialchars($lesson['title']); ?></title>
    <link rel="stylesheet" href="../../../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-left: 6px solid #4a6cf7;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4a6cf7, #6a11cb);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .lesson-info {
            flex: 1;
        }
        
        .back-link {
            color: #4a6cf7;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: #6a11cb;
            transform: translateX(-3px);
        }
        
        .lesson-info h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .lesson-info h2 {
            color: #4a6cf7;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .course-info {
            color: #7f8c8d;
            font-size: 14px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 10px;
        }
        
        /* Upload Card */
        .upload-card {
            background: white;
            border-radius: 15px;
            padding: 35px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            margin-bottom: 25px;
        }
        
        .upload-card h3 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .upload-card h3 i {
            color: #4a6cf7;
        }
        
        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 15px;
        }
        
        .required {
            color: #ff4757;
        }
        
        /* File Upload Area */
        .upload-area {
            border: 3px dashed #d1d8e0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
            position: relative;
            overflow: hidden;
        }
        
        .upload-area:hover {
            border-color: #4a6cf7;
            background: #f0f4ff;
        }
        
        .upload-area.dragover {
            border-color: #4a6cf7;
            background: #e8eeff;
            transform: scale(1.01);
        }
        
        .upload-icon {
            font-size: 64px;
            color: #4a6cf7;
            margin-bottom: 20px;
        }
        
        .upload-text h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .upload-text p {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 15px;
        }
        
        .browse-btn {
            display: inline-block;
            background: #4a6cf7;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .browse-btn:hover {
            background: #3a5ce5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.4);
        }
        
        /* File List */
        .file-list {
            margin-top: 25px;
            display: none;
        }
        
        .file-list.show {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        .file-list-title {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .files-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            padding: 15px;
            background: #fafbfd;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .file-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 18px;
        }
        
        .file-info {
            flex: 1;
            min-width: 0;
        }
        
        .file-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .file-size {
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .remove-file {
            background: #ff4757;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            margin-left: 10px;
        }
        
        .remove-file:hover {
            background: #ff3742;
            transform: scale(1.1);
        }
        
        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eef2f7;
        }
        
        .btn {
            padding: 14px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(74, 108, 247, 0.3);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        /* Supported Files */
        .supported-files {
            background: #f0f7ff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .supported-files h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-types {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .file-type-tag {
            background: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            color: #4a6cf7;
            border: 1px solid #d1d8e0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s;
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
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }
        
        /* File Type Colors */
        .pdf { border-color: #ff4757; }
        .pdf .file-icon { background: #ff4757; }
        
        .document { border-color: #0d6efd; }
        .document .file-icon { background: #0d6efd; }
        
        .presentation { border-color: #fd7e14; }
        .presentation .file-icon { background: #fd7e14; }
        
        .spreadsheet { border-color: #198754; }
        .spreadsheet .file-icon { background: #198754; }
        
        .archive { border-color: #6f42c1; }
        .archive .file-icon { background: #6f42c1; }
        
        .image { border-color: #20c997; }
        .image .file-icon { background: #20c997; }
        
        .text { border-color: #6c757d; }
        .text .file-icon { background: #6c757d; }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .upload-card {
                padding: 25px 20px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .file-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .file-icon {
                margin-right: 0;
            }
            
            .remove-file {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="lesson-info">
                    <a href="?c=material&a=index&lesson_id=<?php echo $lesson['id']; ?>" class="back-link">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách tài liệu
                    </a>
                    
                    <h1>
                        <i class="fas fa-cloud-upload-alt"></i> Upload Tài liệu Mới
                    </h1>
                    
                    <h2>
                        <i class="fas fa-book-open"></i> <?php echo htmlspecialchars($lesson['title']); ?>
                    </h2>
                    
                    <div class="course-info">
                        <i class="fas fa-graduation-cap"></i> 
                        Khóa học: <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                        • Bài học #<?php echo $lesson['order'] ?? 1; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Upload Form -->
        <div class="upload-card">
            <h3>
                <i class="fas fa-file-upload"></i> Chọn file để upload
            </h3>
            
            <form action="?c=material&a=store" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                
                <!-- File Upload Area -->
                <div class="form-group">
                    <label class="form-label">
                        Tài liệu <span class="required">*</span>
                        <small>(Có thể chọn nhiều file)</small>
                    </label>
                    
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
                               multiple 
                               style="display: none;"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.jpg,.jpeg,.png,.gif">
                        <button type="button" class="browse-btn" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-folder-open"></i> Chọn File
                        </button>
                    </div>
                    
                    <!-- Selected Files List -->
                    <div class="file-list" id="fileList">
                        <h4 class="file-list-title">
                            <i class="fas fa-list"></i> File đã chọn
                        </h4>
                        <div class="files-container" id="filesContainer">
                            <!-- Files will be added here dynamically -->
                        </div>
                    </div>
                </div>
                
                <!-- Supported File Types -->
                <div class="supported-files">
                    <h4>
                        <i class="fas fa-check-circle"></i> Định dạng file được hỗ trợ
                    </h4>
                    <div class="file-types">
                        <span class="file-type-tag">
                            <i class="fas fa-file-pdf"></i> PDF
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-word"></i> DOC/DOCX
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-powerpoint"></i> PPT/PPTX
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-excel"></i> XLS/XLSX
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-archive"></i> ZIP/RAR
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-alt"></i> TXT
                        </span>
                        <span class="file-type-tag">
                            <i class="fas fa-file-image"></i> JPG/PNG/GIF
                        </span>
                    </div>
                    <p style="margin-top: 10px; font-size: 13px; color: #6c757d;">
                        <i class="fas fa-info-circle"></i> 
                        Kích thước tối đa: 50MB/file • Có thể upload nhiều file cùng lúc
                    </p>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="?c=material&a=index&lesson_id=<?php echo $lesson['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-upload"></i> Upload Tài liệu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const fileList = document.getElementById('fileList');
            const filesContainer = document.getElementById('filesContainer');
            const submitBtn = document.getElementById('submitBtn');
            let selectedFiles = [];
            
            // Drag & Drop Events
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
                uploadArea.classList.add('dragover');
            }
            
            function unhighlight() {
                uploadArea.classList.remove('dragover');
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
            
            // Browse button click
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Handle selected files
            function handleFiles(files) {
                selectedFiles = Array.from(files);
                
                if (selectedFiles.length > 0) {
                    updateFileList();
                    fileList.classList.add('show');
                } else {
                    fileList.classList.remove('show');
                }
                
                updateSubmitButton();
            }
            
            // Update file list display
            function updateFileList() {
                filesContainer.innerHTML = '';
                
                selectedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item ' + getFileTypeClass(file.name);
                    
                    const fileExtension = getFileExtension(file.name);
                    const fileIcon = getFileIcon(fileExtension);
                    
                    fileItem.innerHTML = `
                        <div class="file-icon">
                            <i class="${fileIcon}"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name" title="${file.name}">
                                ${file.name}
                            </div>
                            <div class="file-size">
                                ${formatFileSize(file.size)}
                            </div>
                        </div>
                        <button type="button" class="remove-file" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    filesContainer.appendChild(fileItem);
                });
            }
            
            // Remove file from list
            window.removeFile = function(index) {
                selectedFiles.splice(index, 1);
                
                // Update data transfer
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
                
                updateFileList();
                if (selectedFiles.length === 0) {
                    fileList.classList.remove('show');
                }
                updateSubmitButton();
            };
            
            // Update submit button state
            function updateSubmitButton() {
                if (selectedFiles.length > 0) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<i class="fas fa-upload"></i> Upload ${selectedFiles.length} file`;
                } else {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fas fa-upload"></i> Upload Tài liệu`;
                }
            }
            
            // Get file extension
            function getFileExtension(filename) {
                return filename.split('.').pop().toLowerCase();
            }
            
            // Get file type class
            function getFileTypeClass(filename) {
                const ext = getFileExtension(filename);
                const typeMap = {
                    'pdf': 'pdf',
                    'doc': 'document',
                    'docx': 'document',
                    'ppt': 'presentation',
                    'pptx': 'presentation',
                    'xls': 'spreadsheet',
                    'xlsx': 'spreadsheet',
                    'zip': 'archive',
                    'rar': 'archive',
                    'txt': 'text',
                    'jpg': 'image',
                    'jpeg': 'image',
                    'png': 'image',
                    'gif': 'image'
                };
                return typeMap[ext] || 'other';
            }
            
            // Get file icon based on extension
            function getFileIcon(extension) {
                const iconMap = {
                    'pdf': 'fas fa-file-pdf',
                    'doc': 'fas fa-file-word',
                    'docx': 'fas fa-file-word',
                    'ppt': 'fas fa-file-powerpoint',
                    'pptx': 'fas fa-file-powerpoint',
                    'xls': 'fas fa-file-excel',
                    'xlsx': 'fas fa-file-excel',
                    'zip': 'fas fa-file-archive',
                    'rar': 'fas fa-file-archive',
                    'txt': 'fas fa-file-alt',
                    'jpg': 'fas fa-file-image',
                    'jpeg': 'fas fa-file-image',
                    'png': 'fas fa-file-image',
                    'gif': 'fas fa-file-image'
                };
                return iconMap[extension] || 'fas fa-file';
            }
            
            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            // Form submission
            document.getElementById('uploadForm').addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang upload...';
                submitBtn.disabled = true;
                
                // Validate file size
                const maxSize = 50 * 1024 * 1024; // 50MB
                for (let file of selectedFiles) {
                    if (file.size > maxSize) {
                        e.preventDefault();
                        alert(`File "${file.name}" vượt quá kích thước 50MB cho phép`);
                        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Tài liệu';
                        submitBtn.disabled = false;
                        return;
                    }
                }
                
                // Validate file types
                const allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                for (let file of selectedFiles) {
                    const ext = getFileExtension(file.name);
                    if (!allowedExtensions.includes(ext)) {
                        e.preventDefault();
                        alert(`File "${file.name}" có định dạng không được hỗ trợ`);
                        submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Tài liệu';
                        submitBtn.disabled = false;
                        return;
                    }
                }
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>