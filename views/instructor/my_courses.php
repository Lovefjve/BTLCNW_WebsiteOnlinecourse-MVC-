<?php include '../layouts/header.php'; ?>

<div class="dashboard">

  <div class="content">
    <div class="panel">
      <div class="panel-head">
        <h3>Khóa học của tôi</h3>
        <a class="btn btn-primary" href="course/create.php">+ Tạo khóa</a>
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
          <tr>
            <td>PHP Cơ Bản</td>
            <td>Lập trình</td>
            <td>Đã duyệt</td>
            <td>
              <a class="btn small btn-outline" href="course/edit.php">Sửa</a>
              <button class="btn small btn-ghost js-delete">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</div>

<?php include '../layouts/footer.php'; ?>
