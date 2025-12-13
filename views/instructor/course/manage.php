<?php include '../../layouts/header.php'; ?>

<div class="dashboard">

  <div class="content">
    <div class="panel">
      <div class="panel-head">
        <h3>Quản lý khóa học</h3>
        <a class="btn btn-primary" href="create.php">+ Tạo khóa học</a>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Tên khóa học</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Bài học</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PHP Cơ Bản</td>
            <td>Lập trình</td>
            <td><span class="badge success">Đã duyệt</span></td>
            <td>12</td>
            <td>
              <a class="btn small btn-outline" href="edit.php">Sửa</a>
              <a class="btn small btn-outline" href="../lessons/manage.php">Bài học</a>
              <button class="btn small btn-ghost js-delete">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</div>

<?php include '../../layouts/footer.php'; ?>
