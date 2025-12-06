<?php
$title = "Sửa Khóa Học: " . ($course['title'] ?? '');
ob_start();
?>

<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Sửa Khóa Học: <span class="text-primary"><?php echo htmlspecialchars($course['title'] ?? ''); ?></span>
        </h1>
        <a href="/instructor/courses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Chỉnh Sửa Thông Tin</h6>
            <span class="badge bg-<?php echo $course['is_published'] ? 'success' : 'warning'; ?>">
                <?php echo $course['is_published'] ? 'Đã xuất bản' : 'Nháp'; ?>
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="/instructor/courses/<?php echo $course['id']; ?>/update" 
                  enctype="multipart/form-data" id="courseForm">
                
                <!-- Hidden field for current image -->
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($course['image'] ?? ''); ?>">

                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Tiêu đề khóa học</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($course['title'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô tả khóa học</label>
                            <textarea class="form-control" id="description" 
                                      name="description" rows="5" required><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Course Image Upload -->
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">Ảnh đại diện</label>
                            <div class="border rounded p-3 text-center">
                                <div class="mb-3">
                                    <img id="imagePreview" 
                                         src="<?php echo !empty($course['image']) ? htmlspecialchars($course['image']) : '/assets/images/default-course.jpg'; ?>" 
                                         alt="Preview" class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                                <div class="mb-2">
                                    <input type="file" class="form-control" id="image" 
                                           name="image" accept="image/*">
                                </div>
                                <?php if (!empty($course['image'])): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="remove_image" name="remove_image" value="1">
                                        <label class="form-check-label" for="remove_image">
                                            Xóa ảnh hiện tại
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($course['category_id'] ?? 0) == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="level" class="form-label">Cấp độ</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="Beginner" <?php echo ($course['level'] ?? '') == 'Beginner' ? 'selected' : ''; ?>>
                                    Beginner - Người mới bắt đầu
                                </option>
                                <option value="Intermediate" <?php echo ($course['level'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>
                                    Intermediate - Trung cấp
                                </option>
                                <option value="Advanced" <?php echo ($course['level'] ?? '') == 'Advanced' ? 'selected' : ''; ?>>
                                    Advanced - Nâng cao
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Price and Duration -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price" class="form-label">Giá khóa học (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" 
                                       name="price" min="0" step="1000" 
                                       value="<?php echo $course['price'] ?? 0; ?>">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration_weeks" class="form-label">Thời lượng (tuần)</label>
                            <input type="number" class="form-control" id="duration_weeks" 
                                   name="duration_weeks" min="1" max="52" 
                                   value="<?php echo $course['duration_weeks'] ?? 4; ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between pt-4 border-top">
                    <div>
                        <a href="/instructor/courses/<?php echo $course['id']; ?>/delete" 
                           class="btn btn-danger" 
                           onclick="return confirm('Bạn có chắc chắn muốn xóa khóa học này?')">
                            <i class="fas fa-trash me-2"></i>Xóa Khóa Học
                        </a>
                    </div>
                    <div>
                        <a href="/instructor/courses" class="btn btn-light me-2">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập Nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/layout.php';
?>