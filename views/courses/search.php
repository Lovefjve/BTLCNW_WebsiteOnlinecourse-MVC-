<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<section class="courses-page container">
  <h1 class="page-title">Kết quả tìm kiếm</h1>
  <p class="muted">Từ khóa: "React"</p>

  <div class="courses-grid">
    <?php for ($i = 0; $i < 3; $i++): ?>
      <div class="course-card">
        <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=800">
        <div class="course-info">
          <span class="course-badge">Frontend</span>
          <h3>React chuyên sâu</h3>
          <p class="meta">Trần Thị B</p>
          <a href="detail.php" class="btn btn-outline small">Chi tiết</a>
        </div>
      </div>
    <?php endfor; ?>
  </div>
</section>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
