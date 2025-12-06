<?php
// views/instructor/courses/create.php
$root_path = '../../../../';

// Lấy dữ liệu từ Controller
$categories = $categories ?? [];
$errors = $_SESSION['errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];

// Clear session data sau khi dùng
unset($_SESSION['errors']);
unset($_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Khóa Học Mới</title>
    
    <link rel="stylesheet" href="<?php echo $root_path; ?>btl/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
  
</head>
<body>
    <div class="instructor-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-plus-circle"></i> Tạo Khóa Học Mới</h1>
            <a href="../course/manage.php" class="create-btn">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        
        <!-- Alert Messages -->
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
        
        <div class="create-course-form">
            <form method="POST" enctype="multipart/form-data" id="courseForm">
                <div class="form-grid">
                    <!-- Title -->
                    <div class="form-group required <?php echo isset($errors['title']) ? 'has-error' : ''; ?>">
                        <label for="title">Tên khóa học</label>
                        <input type="text" id="title" name="title" 
                               value="<?php echo htmlspecialchars($old_input['title'] ?? ''); ?>"
                               placeholder="Ví dụ: Lập trình PHP cơ bản" required>
                        <?php if (isset($errors['title'])): ?>
                            <span class="error-message"><?php echo $errors['title']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Category -->
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
                    
                    <!-- Price -->
                    <div class="form-group <?php echo isset($errors['price']) ? 'has-error' : ''; ?>">
                        <label for="price">Giá (VNĐ)</label>
                        <input type="number" id="price" name="price" 
                               value="<?php echo htmlspecialchars($old_input['price'] ?? 0); ?>"
                               placeholder="0 = Miễn phí" min="0" step="1000">
                        <?php if (isset($errors['price'])): ?>
                            <span class="error-message"><?php echo $errors['price']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Duration -->
                    <div class="form-group <?php echo isset($errors['duration_weeks']) ? 'has-error' : ''; ?>">
                        <label for="duration_weeks">Thời lượng (tuần)</label>
                        <input type="number" id="duration_weeks" name="duration_weeks" 
                               value="<?php echo htmlspecialchars($old_input['duration_weeks'] ?? 4); ?>"
                               min="1" max="52">
                        <?php if (isset($errors['duration_weeks'])): ?>
                            <span class="error-message"><?php echo $errors['duration_weeks']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Level -->
                    <div class="form-group">
                        <label for="level">Cấp độ</label>
                        <select id="level" name="level">
                            <option value="Beginner" <?php echo ($old_input['level'] ?? '') == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="Intermediate" <?php echo ($old_input['level'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="Advanced" <?php echo ($old_input['level'] ?? '') == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-group">
                        <label for="image">Ảnh bìa</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <div class="file-preview">
                            <img id="imagePreview" src="#" alt="Preview">
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group required full-width <?php echo isset($errors['description']) ? 'has-error' : ''; ?>">
                        <label for="description">Mô tả khóa học</label>
                        <textarea id="description" name="description" rows="6" 
                                  placeholder="Mô tả chi tiết về khóa học..." required><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <span class="error-message"><?php echo $errors['description']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="create-btn">
                        <i class="fas fa-save"></i> Lưu khóa học
                    </button>
                    <a href="../course/manage.php" class="btn-cancel">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Preview ảnh
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
        
        // Validation client-side
        document.getElementById('courseForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            
            if (title.length < 5) {
                e.preventDefault();
                alert('Tên khóa học phải có ít nhất 5 ký tự');
                return false;
            }
            
            if (description.length < 20) {
                e.preventDefault();
                alert('Mô tả phải có ít nhất 20 ký tự');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>