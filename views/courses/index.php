<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<section class="courses-page container">
  <h1 class="page-title">Danh sách khóa học</h1>

  <div class="courses-grid">
    <?php for ($i = 0; $i < 6; $i++): ?>
      <div class="course-card">
        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800">
        <div class="course-info">
          <span class="course-badge">Lập trình</span>
          <h3>React từ cơ bản đến nâng cao</h3>
          <p class="meta">Nguyễn Văn A • 40 giờ</p>
          <div class="course-price">
            <span class="price-current">1.500.000đ</span>
            <span class="price-original">2.500.000đ</span>
          </div>
          <a href="detail.php" class="btn btn-primary small">Xem chi tiết</a>
        </div>
      </div>
    <?php endfor; ?>
  </div>
</section>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
