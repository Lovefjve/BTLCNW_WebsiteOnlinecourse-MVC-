<?php
// views/instructor/courses/create.php
session_start();

// Kiểm tra quyền
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: /onlinecourse/login.php');
    exit;
}

require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Khóa Học Mới - Giảng viên</title>
    <link rel="stylesheet" href="/onlinecourse/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .create-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-left: 5px solid #4a6cf7;
        }
        
        .page-header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header p {
            color: #7f8c8d;
            margin-top: 10px;
            font-size: 15px;
        }
        
        .create-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
        }
        
        .form-group label.required::after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fff;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }
        
        .form-control.error {
            border-color: #e74c3c;
        }
        
        .form-text {
            margin-top: 6px;
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
        }
        
        .file-upload {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        .file-upload:hover {
            border-color: #4a6cf7;
            background: #f5f7ff;
        }
        
        .file-upload i {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
        
        .file-upload p {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        .file-preview {
            margin-top: 15px;
            display: none;
        }
        
        .file-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="create-container">
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới</h1>
            <p>Điền đầy đủ thông tin để tạo khóa học mới. Bạn có thể lưu bản nháp và chỉnh sửa sau.</p>
        </div>
        
        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form class="create-form" action="/onlinecourse/instructor/courses/store" method="POST" enctype="multipart/form-data">
            <!-- Basic Information -->
            <div class="form-group">
                <label class="required" for="title">Tiêu đề khóa học</label>
                <input type="text" id="title" name="title" class="form-control" 
                       placeholder="Ví dụ: Lập trình PHP từ cơ bản đến nâng cao" required>
                <div class="form-text">Tiêu đề hấp dẫn sẽ thu hút nhiều học viên hơn</div>
            </div>
            
            <div class="form-group">
                <label for="short_description">Mô tả ngắn</label>
                <textarea id="short_description" name="short_description" class="form-control" 
                          rows="3" placeholder="Mô tả ngắn gọn về khóa học (hiển thị ở danh sách)"></textarea>
            </div>
            
            <div class="form-group">
                <label class="required" for="description">Mô tả chi tiết</label>
                <textarea id="description" name="description" class="form-control" 
                          rows="10" placeholder="Mô tả chi tiết về khóa học, nội dung sẽ học, lợi ích..." required></textarea>
            </div>
            
            <!-- Course Details -->
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="level">Trình độ</label>
                    <select id="level" name="level" class="form-control">
                        <option value="Beginner">Beginner (Cơ bản)</option>
                        <option value="Intermediate">Intermediate (Trung cấp)</option>
                        <option value="Advanced">Advanced (Nâng cao)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="duration_weeks">Thời lượng (tuần)</label>
                    <input type="number" id="duration_weeks" name="duration_weeks" 
                           class="form-control" min="1" max="52" value="4">
                </div>
            </div>
            
            <!-- Price -->
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Giá khóa học (VNĐ)</label>
                    <input type="number" id="price" name="price" class="form-control" 
                           min="0" step="1000" value="0" placeholder="0 = Miễn phí">
                    <div class="form-text">Để 0 nếu là khóa học miễn phí</div>
                </div>
            </div>
            
            <!-- Course Image -->
            <div class="form-group">
                <label for="image">Hình ảnh khóa học</label>
                <div class="file-upload" id="imageUpload">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Kéo thả hình ảnh vào đây hoặc click để chọn</p>
                    <p class="form-text">Kích thước đề xuất: 800x450px (tỷ lệ 16:9)</p>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <div class="file-preview" id="imagePreview">
                    <img id="previewImage" src="" alt="Preview">
                </div>
            </div>
            
            <!-- Requirements & Outcomes -->
            <div class="form-row">
                <div class="form-group">
                    <label for="requirements">Yêu cầu trước khóa học</label>
                    <textarea id="requirements" name="requirements" class="form-control" 
                              rows="4" placeholder="Ví dụ: Biết cơ bản về lập trình, có máy tính kết nối internet..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="learning_outcomes">Kết quả đạt được</label>
                    <textarea id="learning_outcomes" name="learning_outcomes" class="form-control" 
                              rows="4" placeholder="Ví dụ: Có thể xây dựng website bằng PHP, hiểu về CSDL MySQL..."></textarea>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu và Tạo Khóa Học
                </button>
                <a href="/onlinecourse/instructor/courses" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy bỏ
                </a>
            </div>
        </form>
    </div>
    
    <!-- Summernote WYSIWYG Editor -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('#description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Image upload preview
            const imageUpload = document.getElementById('imageUpload');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            
            imageUpload.addEventListener('click', () => imageInput.click());
            
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Drag and drop for image
            imageUpload.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageUpload.style.borderColor = '#4a6cf7';
                imageUpload.style.background = '#f5f7ff';
            });
            
            imageUpload.addEventListener('dragleave', () => {
                imageUpload.style.borderColor = '#e0e0e0';
                imageUpload.style.background = '#fafafa';
            });
            
            imageUpload.addEventListener('drop', (e) => {
                e.preventDefault();
                imageUpload.style.borderColor = '#e0e0e0';
                imageUpload.style.background = '#fafafa';
                
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    imageInput.files = e.dataTransfer.files;
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Form validation
            const form = document.querySelector('.create-form');
            form.addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const description = $('#description').summernote('code').replace(/<[^>]*>/g, '').trim();
                
                if (!title) {
                    e.preventDefault();
                    alert('Vui lòng nhập tiêu đề khóa học');
                    document.getElementById('title').focus();
                    return;
                }
                
                if (!description) {
                    e.preventDefault();
                    alert('Vui lòng nhập mô tả chi tiết cho khóa học');
                    $('#description').summernote('focus');
                    return;
                }
            });
        });
    </script>
</body>
</html>