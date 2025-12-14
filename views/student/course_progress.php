<?php include_once __DIR__ . "/../layouts/header.php"; ?>
<?php include_once __DIR__ . "/../layouts/sidebar.php"; ?>

<main class="dashboard">

  <section id="progress" class="panel">
    <h2>Tiến độ học tập</h2>

    <?php if (empty($progressList)): ?>
        <p class="muted">Tiến độ sẽ hiển thị sau khi bạn đăng ký khóa.</p>
    <?php else: ?>
        <ul class="progress-list">
          <?php foreach ($progressList as $item): ?>
              <li class="progress-item">
                <strong><?= $item['course_name'] ?></strong>
                <div class="progress-bar">
                  <div class="bar" style="width: <?= $item['percent'] ?>%"></div>
                </div>
                <span><?= $item['percent'] ?>%</span>
              </li>
          <?php endforeach; ?>
        </ul>
    <?php endif; ?>

  </section>

</main>

<?php include_once __DIR__ . "/../layouts/footer.php"; ?>
