<?php include_once __DIR__ . "/../layouts/header.php"; ?>
<?php include_once __DIR__ . "/../layouts/sidebar.php"; ?>

<main class="dashboard">

  <section id="enrolled" class="panel">
    <h2>Khóa đã đăng ký</h2>

    <div id="enrolledList">
      <?php if (empty($myCourses)): ?>
          <p class="muted">Bạn chưa đăng ký khóa nào.</p>
      <?php else: ?>
          <div class="courses-grid">
            <?php foreach ($myCourses as $course): ?>
              <div class="course-card">
                <div class="course-image">
                  <img src="<?= $course['thumbnail'] ?>" alt="">
                </div>
                <div class="course-content">
                  <h3><?= $course['name'] ?></h3>
                  <p><?= $course['progress'] ?>% hoàn thành</p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
      <?php endif; ?>
    </div>

  </section>

</main>

<?php include_once __DIR__ . "/../layouts/footer.php"; ?>
