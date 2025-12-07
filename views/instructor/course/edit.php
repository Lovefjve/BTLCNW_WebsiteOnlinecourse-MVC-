<?php
// views/instructor/course/edit.php
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
    <title>Chỉnh sửa Khóa Học</title>
    <link rel="stylesheet" href="<?php echo $root_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-form {
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
        
        .current-image {
            margin-top: 10px;
        }
        
        .current-image img {
            max-width: 200px;
            border-radius: 6px;
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
            <h1><i class="fas fa-edit"></i> Chỉnh sửa Khóa Học</h1>
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
        
        <div class="edit-form">
            <form method="POST" action="?c=instructor&a=update" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                
                <div class="form-group required">
                    <label for="title">Tên khóa học</label>
                    <input type="text" id="title" name="title" 
                           value="<?php echo htmlspecialchars($course['title']); ?>"
                           placeholder="Ví dụ: Lập trình PHP cơ bản" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo $course['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Giá (VNĐ)</label>
                    <input type="number" id="price" name="price" 
                           value="<?php echo $course['price']; ?>"
                           placeholder="0 = Miễn phí" min="0" step="1000">
                </div>
                
                <div class="form-group">
                    <label for="duration_weeks">Thời lượng (tuần)</label>
                    <input type="number" id="duration_weeks" name="duration_weeks" 
                           value="<?php echo $course['duration_weeks']; ?>"
                           min="1" max="52">
                </div>
                
                <div class="form-group">
                    <label for="level">Cấp độ</label>
                    <select id="level" name="level">
                        <option value="Beginner" <?php echo $course['level'] == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                        <option value="Intermediate" <?php echo $course['level'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                        <option value="Advanced" <?php echo $course['level'] == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <select id="status" name="status">
                        <option value="draft" <?php echo $course['status'] == 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                        <option value="pending" <?php echo $course['status'] == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="published" <?php echo $course['status'] == 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Ảnh bìa mới (tối đa 5MB)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <?php if (!empty($course['image'])): ?>
                    <div class="current-image">
                        <p>Ảnh hiện tại:</p>
                        <img src="<?php echo $root_path; ?>assets/uploads/courses/<?php echo $course['image']; ?>" 
                             alt="Current image" style="max-width: 200px;">
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group required">
                    <label for="description">Mô tả khóa học</label>
                    <textarea id="description" name="description" rows="6" 
                              placeholder="Mô tả chi tiết về khóa học..." required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Cập nhật khóa học
                    </button>
                    <a href="?c=instructor&a=courses" class="btn-cancel">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>