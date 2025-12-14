document.addEventListener('DOMContentLoaded', () => {

  const navLinks = document.querySelectorAll('.nav-links a');
  navLinks.forEach(link => {
    if (link.href === location.href) {
      link.classList.add('active');
    }
  });

  const sidebarLinks = document.querySelectorAll('.sidebar a');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', () => {
      sidebarLinks.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });


  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Đăng nhập thành công (demo UI)');
      location.href = 'dashboard.php';
    });
  }

  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Đăng ký thành công (demo UI)');
      registerForm.reset();
    });
  }

  const searchInput = document.getElementById('searchCourse');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const keyword = searchInput.value.toLowerCase();
      document.querySelectorAll('.course-card').forEach(card => {
        const title = card.innerText.toLowerCase();
        card.style.display = title.includes(keyword) ? '' : 'none';
      });
    });
  }


  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('enroll')) {
      alert('Đăng ký khóa học thành công (demo UI)');
    }
  });


  const btnAddCourse = document.getElementById('btnAddCourse');
  const courseForm = document.getElementById('courseForm');

  if (btnAddCourse && courseForm) {
    btnAddCourse.addEventListener('click', () => {
      courseForm.classList.toggle('active');
    });
  }


  const instructorForm = document.getElementById('fakeCreateCourse');
  if (instructorForm) {
    instructorForm.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Tạo / cập nhật khóa học thành công (demo UI)');
      instructorForm.reset();
      courseForm.classList.remove('active');
    });
  }


  document.addEventListener('click', (e) => {

    if (e.target.classList.contains('btn-edit')) {
      alert('Sửa khóa học (demo UI)');
    }

    if (e.target.classList.contains('btn-manage')) {
      alert('Quản lý nội dung khóa học (demo UI)');
    }

    if (e.target.classList.contains('btn-delete')) {
      if (confirm('Bạn có chắc muốn xóa khóa học này?')) {
        e.target.closest('.instructor-course-item, .course-card')?.remove();
      }
    }
  });


  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('toggle-user')) {
      const row = e.target.closest('tr');
      const status = row.querySelector('.status');
      if (status.innerText === 'Kích hoạt') {
        status.innerText = 'Đã khóa';
        e.target.innerText = 'Mở';
      } else {
        status.innerText = 'Kích hoạt';
        e.target.innerText = 'Khóa';
      }
    }
  });

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('approve-course')) {
      alert('Duyệt khóa học thành công (demo UI)');
      e.target.closest('tr')?.remove();
    }

    if (e.target.classList.contains('reject-course')) {
      if (confirm('Từ chối khóa học này?')) {
        e.target.closest('tr')?.remove();
      }
    }
  });

});
