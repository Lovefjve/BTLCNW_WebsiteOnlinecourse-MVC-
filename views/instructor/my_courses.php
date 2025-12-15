<?php include '../layouts/header.php'; ?>

<div class="dashboard">

  <div class="content">
    <div class="panel">
      <div class="panel-head">
        <h3>Khóa học của tôi</h3>
        <a class="btn btn-primary" href="<?php echo BASE_URL; ?>/instructor/course/create">+ Tạo khóa</a>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Tên khóa</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($courses)): ?>
            <tr>
              <td colspan="4">Bạn chưa có khóa học nào.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($courses as $course): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['category_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($course['status']); ?></td>
                <td>
                  <a class="btn small btn-outline" href="<?php echo BASE_URL; ?>/instructor/course/edit/<?php echo $course['id']; ?>">Sửa</a>
                  <button class="btn small btn-ghost js-delete" data-id="<?php echo $course['id']; ?>">Xóa</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<?php include '../layouts/footer.php'; ?>
