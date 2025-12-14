<?php include_once __DIR__ . "/../layouts/header.php"; ?>
<?php include_once __DIR__ . "/../layouts/sidebar.php"; ?>

<main class="dashboard">

  <section id="courses" class="panel">
    <div class="panel-head">
      <h2>Danh sách khóa học</h2>

      <div class="filters">
        <input id="searchCourse" placeholder="Tìm khóa học..." />
        <select id="categoryFilter">
          <option value="">Tất cả danh mục</option>
          <option value="laptrinh">Lập trình</option>
          <option value="thietke">Thiết kế</option>
          <option value="marketing">Marketing</option>
          <option value="datasci">Data Science</option>
        </select>
      </div>
    </div>

    <div id="coursesGrid" class="courses-grid">
      <!-- Render danh sách khóa học từ controller -->
      <?php if (!empty($courses)): ?>
          <?php foreach($courses as $course): ?>
              <a href="" class="course-card">
                <div class="course-image">
                  <img src="<?= $course['thumbnail'] ?>" alt="">
                </div>
                <div class="course-content">
                  <h3><?= $course['name'] ?></h3>
                  <p><?= $course['teacher_name'] ?></p>
                </div>
              </a>
          <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </section>

</main>

<?php include_once __DIR__ . "/../layouts/footer.php"; ?>
