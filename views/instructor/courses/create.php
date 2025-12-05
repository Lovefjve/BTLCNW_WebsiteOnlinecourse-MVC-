<?php
$title = "Tạo Khóa Học Mới";
ob_start();
?>

<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tạo Khóa Học Mới</h1>
        <a href="/instructor/courses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại
        </a>
    </div>

    <!-- Form -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông Tin Khóa Học</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="/instructor/courses/store" enctype="multipart/form-data" 
                  id="courseForm" novalidate>
                
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">
                                Tiêu đề khóa học <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($old['title'] ?? ''); ?>" 
                                   required>
                            <div class="invalid-feedback">Vui lòng nhập tiêu đề khóa học</div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">
                                Mô tả khóa học <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" 
                                      name="description" rows="5" required
                                      placeholder="Mô tả chi tiết về khóa học..."><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">Vui lòng nhập mô tả khóa học</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Course Image Upload -->
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">Ảnh đại diện</label>
                            <div class="border rounded p-3 text-center">
                                <div class="mb-3">
                                    <img id="imagePreview" src="/assets/images/default-course.jpg" 
                                         alt="Preview" class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                                <input type="file" class="form-control" id="image" 
                                       name="image" accept="image/*">
                                <div class="form-text">
                                    Kích thước đề xuất: 1280x720px
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id" class="form-label">
                                Danh mục <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo (isset($old['category_id']) && $old['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn danh mục</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="level" class="form-label">
                                Cấp độ <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">Chọn cấp độ</option>
                                <option value="Beginner" <?php echo (isset($old['level']) && $old['level'] == 'Beginner') ? 'selected' : ''; ?>>
                                    Beginner - Người mới bắt đầu
                                </option>
                                <option value="Intermediate" <?php echo (isset($old['level']) && $old['level'] == 'Intermediate') ? 'selected' : ''; ?>>
                                    Intermediate - Trung cấp
                                </option>
                                <option value="Advanced" <?php echo (isset($old['level']) && $old['level'] == 'Advanced') ? 'selected' : ''; ?>>
                                    Advanced - Nâng cao
                                </option>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn cấp độ</div>
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
                                       value="<?php echo $old['price'] ?? 0; ?>">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <div class="form-text">
                                Để 0 nếu khóa học miễn phí
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration_weeks" class="form-label">
                                Thời lượng (tuần) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="duration_weeks" 
                                   name="duration_weeks" min="1" max="52" 
                                   value="<?php echo $old['duration_weeks'] ?? 4; ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập thời lượng hợp lệ (1-52 tuần)</div>
                        </div>
                    </div>
                </div>

                <!-- Requirements and Outcomes -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="requirements" class="form-label">Yêu cầu trước khóa học</label>
                            <textarea class="form-control" id="requirements" 
                                      name="requirements" rows="3"
                                      placeholder="Ví dụ: Cần biết HTML cơ bản..."><?php echo htmlspecialchars($old['requirements'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="outcomes" class="form-label">Kết quả đạt được</label>
                            <textarea class="form-control" id="outcomes" 
                                      name="outcomes" rows="3"
                                      placeholder="Ví dụ: Có thể xây dựng website cơ bản..."><?php echo htmlspecialchars($old['outcomes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Publish Options -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Tùy chọn xuất bản</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="is_published" 
                                   name="is_published" value="1" 
                                   <?php echo (isset($old['is_published']) && $old['is_published']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_published">
                                Xuất bản ngay (Hiển thị công khai)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured" 
                                   name="featured" value="1" 
                                   <?php echo (isset($old['featured']) && $old['featured']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">
                                Đánh dấu là nổi bật
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" name="save_draft" value="1" 
                            class="btn btn-secondary">
                        <i class="fas fa-save me-2"></i>Lưu nháp
                    </button>
                    <div>
                        <a href="/instructor/courses" class="btn btn-light me-2">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="submit" name="save_publish" value="1" 
                                class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Tạo khóa học
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

