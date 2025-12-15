<?php
// views/instructor/lessons/create.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Bài học Mới - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-left: 5px solid #2ecc71;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 10px 0;
        }
        
        .header h3 {
            margin: 0;
            color: #4a6cf7;
            font-weight: 600;
            font-size: 18px;
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: 'Segoe UI', 'Inter', sans-serif;
        }
        
        .form-control:focus {
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
            outline: none;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
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
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
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
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .container {
                padding: 10px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h1>
                        <i class="fas fa-plus-circle"></i> Tạo Bài học Mới
                    </h1>
                    <h3>
                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($course['title']); ?>
                    </h3>
                </div>
                <a href="<?php echo BASE_URL; ?>/instructor/lessons/manage?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="<?php echo BASE_URL; ?>/instructor/lessons/store" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                
                <div class="form-group <?php echo isset($errors['title']) ? 'has-error' : ''; ?>">
                    <label class="form-label" for="title">Tiêu đề bài học *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($old_input['title'] ?? ''); ?>"
                           placeholder="Ví dụ: Giới thiệu về HTML5"
                           required
                           maxlength="255">
                    <?php if (isset($errors['title'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['title']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['order']) ? 'has-error' : ''; ?>">
                    <label class="form-label" for="order">Thứ tự bài học</label>
                    <input type="number" 
                           id="order" 
                           name="order" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($old_input['order'] ?? $next_order); ?>"
                           min="1" 
                           max="100"
                           required>
                    <?php if (isset($errors['order'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['order']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['video_url']) ? 'has-error' : ''; ?>">
                    <label class="form-label" for="video_url">
                        <i class="fas fa-video"></i> URL Video (tùy chọn)
                    </label>
                    <input type="url" 
                           id="video_url" 
                           name="video_url" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($old_input['video_url'] ?? ''); ?>"
                           placeholder="https://www.youtube.com/watch?v=..."
                           maxlength="255">
                    <?php if (isset($errors['video_url'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['video_url']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['content']) ? 'has-error' : ''; ?>">
                    <label class="form-label" for="content">
                        <i class="fas fa-file-alt"></i> Nội dung bài học *
                    </label>
                    <textarea id="content" 
                              name="content" 
                              class="form-control" 
                              rows="12"
                              placeholder="Nhập nội dung chi tiết của bài học..."
                              required><?php echo htmlspecialchars($old_input['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['content']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Lưu Bài học
                    </button>
                    <a href="<?php echo BASE_URL; ?>/instructor/lessons/manage?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Auto-calculate next lesson order
        document.addEventListener('DOMContentLoaded', function() {
            const orderInput = document.getElementById('order');
            
            if (!orderInput.value.trim()) {
                orderInput.value = <?php echo $next_order ?? 1; ?>;
            }
            
            // Tự động focus vào ô tiêu đề
            document.getElementById('title').focus();
        });
    </script>
</body>
</html>