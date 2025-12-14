<?php include_once "./../layouts/header.php"; ?>

<section class="popular-courses">
      <div class="container">
        <div class="section-header-flex">
          <div>
            <h2>Khóa học phổ biến</h2>
            <p>Những khóa học được yêu thích nhất</p>
          </div>
          <a href="courses.html" class="btn btn-outline">
            Xem tất cả <i class="fas fa-arrow-right"></i>
          </a>
        </div>
        <div class="courses-grid">
          <a href="course-detail.html?id=1" class="course-card">
            <div class="course-image">
              <img src="https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=300&h=200&fit=crop" alt="React">
              <span class="course-badge">Lập trình</span>
            </div>
            <div class="course-content">
              <h3>Lập trình React từ cơ bản đến nâng cao</h3>
              <p class="course-instructor">Nguyễn Văn A</p>
              <div class="course-meta">
                <span><i class="fas fa-star"></i> 4.8</span>
                <span><i class="fas fa-users"></i> 1,234</span>
                <span><i class="fas fa-clock"></i> 40 giờ</span>
              </div>
              <div class="course-price">
                <span class="price-current">1.500.000đ</span>
                <span class="price-original">2.500.000đ</span>
              </div>
            </div>
          </a>
          <a href="course-detail.html?id=2" class="course-card">
            <div class="course-image">
              <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=300&h=200&fit=crop" alt="UI/UX">
              <span class="course-badge">Thiết kế</span>
            </div>
            <div class="course-content">
              <h3>Thiết kế UI/UX chuyên nghiệp với Figma</h3>
              <p class="course-instructor">Trần Thị B</p>
              <div class="course-meta">
                <span><i class="fas fa-star"></i> 4.9</span>
                <span><i class="fas fa-users"></i> 856</span>
                <span><i class="fas fa-clock"></i> 35 giờ</span>
              </div>
              <div class="course-price">
                <span class="price-current">1.200.000đ</span>
                <span class="price-original">2.000.000đ</span>
              </div>
            </div>
          </a>
          <a href="course-detail.html?id=3" class="course-card">
            <div class="course-image">
              <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=200&fit=crop" alt="Marketing">
              <span class="course-badge">Marketing</span>
            </div>
            <div class="course-content">
              <h3>Digital Marketing toàn diện</h3>
              <p class="course-instructor">Lê Văn C</p>
              <div class="course-meta">
                <span><i class="fas fa-star"></i> 4.7</span>
                <span><i class="fas fa-users"></i> 2,156</span>
                <span><i class="fas fa-clock"></i> 50 giờ</span>
              </div>
              <div class="course-price">
                <span class="price-current">1.800.000đ</span>
                <span class="price-original">3.000.000đ</span>
              </div>
            </div>
          </a>
          <a href="course-detail.html?id=4" class="course-card">
            <div class="course-image">
              <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop" alt="Python">
              <span class="course-badge">Data Science</span>
            </div>
            <div class="course-content">
              <h3>Python cho Data Science</h3>
              <p class="course-instructor">Phạm Thị D</p>
              <div class="course-meta">
                <span><i class="fas fa-star"></i> 4.9</span>
                <span><i class="fas fa-users"></i> 1,567</span>
                <span><i class="fas fa-clock"></i> 60 giờ</span>
              </div>
              <div class="course-price">
                <span class="price-current">2.000.000đ</span>
                <span class="price-original">3.500.000đ</span>
              </div>
            </div>
          </a>
        </div>
      </div>
    </section>


<?php include_once "./../layouts/footer.php"; ?>
