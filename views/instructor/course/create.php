<?php
// views/instructor/course/create.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = '../../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Khóa Học Mới</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .create-form {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group.required label::after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
            color: white;
        }
        
        .file-preview {
            margin-top: 10px;
        }
        
        .file-preview img {
            max-width: 200px;
            border-radius: 6px;
            display: none;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 5px solid #4a6cf7;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
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
        }
        
        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới</h1>
            <a href="?c=instructor&a=courses" class="btn">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
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
        
        <div class="create-form">
            <form method="POST" action="?c=instructor&a=storeCourse" enctype="multipart/form-data">
                <div class="form-group required <?php echo isset($errors['title']) ? 'has-error' : ''; ?>">
                    <label for="title">Tên khóa học</label>
                    <input type="text" id="title" name="title" 
                           value="<?php echo htmlspecialchars($old_input['title'] ?? ''); ?>"
                           placeholder="Ví dụ: Lập trình PHP cơ bản" required>
                    <?php if (isset($errors['title'])): ?>
                        <span class="error-message"><?php echo $errors['title']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo ($old_input['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group <?php echo isset($errors['price']) ? 'has-error' : ''; ?>">
                    <label for="price">Giá (VNĐ)</label>
                    <input type="number" id="price" name="price" 
                           value="<?php echo htmlspecialchars($old_input['price'] ?? 0); ?>"
                           placeholder="0 = Miễn phí" min="0" step="1000">
                    <?php if (isset($errors['price'])): ?>
                        <span class="error-message"><?php echo $errors['price']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['duration_weeks']) ? 'has-error' : ''; ?>">
                    <label for="duration_weeks">Thời lượng (tuần)</label>
                    <input type="number" id="duration_weeks" name="duration_weeks" 
                           value="<?php echo htmlspecialchars($old_input['duration_weeks'] ?? 4); ?>"
                           min="1" max="52">
                    <?php if (isset($errors['duration_weeks'])): ?>
                        <span class="error-message"><?php echo $errors['duration_weeks']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="level">Cấp độ</label>
                    <select id="level" name="level">
                        <option value="Beginner" <?php echo ($old_input['level'] ?? '') == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                        <option value="Intermediate" <?php echo ($old_input['level'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="Advanced" <?php echo ($old_input['level'] ?? '') == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Ảnh bìa (tối đa 5MB)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div class="file-preview">
                        <img id="imagePreview" src="#" alt="Preview">
                    </div>
                    <?php if (isset($errors['image'])): ?>
                        <span class="error-message"><?php echo $errors['image']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group required <?php echo isset($errors['description']) ? 'has-error' : ''; ?>">
                    <label for="description">Mô tả khóa học</label>
                    <textarea id="description" name="description" rows="6" 
                              placeholder="Mô tả chi tiết về khóa học..." required><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <span class="error-message"><?php echo $errors['description']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Lưu khóa học
                    </button>
                    <a href="?c=instructor&a=courses" class="btn-cancel">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Preview image
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>