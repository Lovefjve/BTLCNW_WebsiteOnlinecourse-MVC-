<?php include_once "./views/layouts/header.php"; ?>

<div class="course-grid">
    <?php for ($i = 1; $i <= 9; $i++): ?>
        <div class="course-card">
            <div class="img"></div>
            <p class="title">Tên khóa học <?php echo $i; ?></p>
        </div>
    <?php endfor; ?>
</div>

<?php include_once "./views/layouts/footer.php"; ?>
