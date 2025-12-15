<?php
// views/instructor/lessons/edit.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Bài học - <?php echo htmlspecialchars($lesson['title']); ?></title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
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
            border-left: 5px solid #17a2b8;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .lesson-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .meta-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: #4a6cf7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .meta-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .meta-value {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
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
            
            .lesson-meta {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-actions > div {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 style="margin: 0 0 10px; color: #2c3e50;">
                        <i class="fas fa-edit"></i> Chỉnh sửa Bài học
                    </h1>
                    <h3 style="margin: 0; color: #4a6cf7; font-weight: 600;">
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="lesson-meta">
            <div class="meta-item">
                <div class="meta-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div>
                    <div class="meta-label">ID Bài học</div>
                    <div class="meta-value">#<?php echo $lesson['id']; ?></div>
                </div>
            </div>
            
            <div class="meta-item">
                <div class="meta-icon" style="background: #2ecc71;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div class="meta-label">Ngày tạo</div>
                    <div class="meta-value">
                        <?php 
                        if (!empty($lesson['created_at'])) {
                            echo date('d/m/Y H:i', strtotime($lesson['created_at']));
                        } else {
                            echo 'Chưa có';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="meta-item">
                <div class="meta-icon" style="background: #17a2b8;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="meta-label">Thứ tự</div>
                    <div class="meta-value">
                        <?php echo $lesson['order'] ?? 1; ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($lesson['video_url'])): ?>
            <div class="meta-item">
                <div class="meta-icon" style="background: #e74c3c;">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <div class="meta-label">Video</div>
                    <div class="meta-value">
                        <a href="<?php echo htmlspecialchars($lesson['video_url']); ?>" target="_blank" style="color: #4a6cf7;">
                            Xem video
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="form-container">
            <form action="<?php echo BASE_URL; ?>/instructor/lessons/update" method="POST">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                
                <div class="form-group">
                    <label class="form-label" for="title">Tiêu đề bài học *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($old_input['title'] ?? $lesson['title']); ?>"
                           required
                           maxlength="255">
                    <?php if (isset($errors['title'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['title']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="order">Thứ tự bài học *</label>
                        <input type="number" 
                               id="order" 
                               name="order" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($old_input['order'] ?? $lesson['order'] ?? 1); ?>"
                               min="1" 
                               max="999"
                               required>
                        <?php if (isset($errors['order'])): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $errors['order']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="video_url">
                            <i class="fas fa-video"></i> URL Video (tùy chọn)
                        </label>
                        <input type="url" 
                               id="video_url" 
                               name="video_url" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($old_input['video_url'] ?? $lesson['video_url'] ?? ''); ?>"
                               placeholder="https://www.youtube.com/watch?v=..."
                               maxlength="255">
                        <?php if (isset($errors['video_url'])): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $errors['video_url']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="content">
                        <i class="fas fa-file-alt"></i> Nội dung bài học *
                    </label>
                    
                    
                    <textarea id="content" 
                              name="content" 
                              class="form-control" 
                              rows="12"
                              placeholder="Nhập nội dung chi tiết của bài học..."
                              required><?php echo htmlspecialchars($old_input['content'] ?? $lesson['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['content']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <div>
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> Cập nhật bài học
                        </button>
                    </div>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/instructor/lessons/manage?course_id=<?php echo $course['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </a>
                    </div>

                </div>
            </form>
            
            <!-- Form ẩn để xóa -->
            <form id="delete-form" action="<?php echo BASE_URL; ?>/instructor/lessons/delete" method="POST" style="display: none;">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
            </form>
        </div>
    </div>
    
    <script>
        function confirmDelete() {
            if (confirm('Bạn có chắc muốn xóa bài học này?\nTất cả tài liệu liên quan cũng sẽ bị xóa.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        // Auto focus vào ô tiêu đề
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('title').focus();
        });
        
        // Xác nhận khi rời trang nếu có thay đổi
        let formChanged = false;
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                formChanged = true;
            });
        });
        
        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Bạn có thay đổi chưa lưu. Bạn có chắc muốn rời đi?';
            }
        });
        
        // Reset formChanged khi submit
        form.addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
</body>
</html>