<?php include '../../layouts/header.php'; ?>

<div class="dashboard">

  <div class="content">
    <div class="panel">
      <h3>Chỉnh sửa khóa học</h3>

      <form method="post">
        <label>Tên khóa học</label>
        <input type="text">

        <label>Danh mục</label>
        <select>
          <option selected>Lập trình</option>
          <option>Thiết kế</option>
        </select>

        <label>Mô tả</label>
        <textarea rows="4"></textarea>

        <button class="btn btn-primary">Cập nhật</button>
      </form>
    </div>
  </div>
</div>

<?php include '../../layouts/footer.php'; ?>
