<?php include '../../layouts/header.php'; ?>

<div class="dashboard">
  <?php include '../../layouts/sidebar_instructor.php'; ?>

  <div class="content">
    <div class="panel">
      <div class="panel-head">
        <h3>Bài học – PHP Cơ Bản</h3>
        <a class="btn btn-primary" href="create.php">+ Thêm bài học</a>
      </div>

      <table class="table">
        <tr>
          <th>Tiêu đề</th>
          <th>Thời lượng</th>
          <th>Hành động</th>
        </tr>
        <tr>
          <td>Giới thiệu PHP</td>
          <td>10 phút</td>
          <td>
            <a class="btn small btn-outline" href="edit.php">Sửa</a>
            <button class="btn small btn-ghost js-delete">Xóa</button>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>

<?php include '../../layouts/footer.php'; ?>
