<?php require_once __DIR__ . "/../../layouts/header.php"; ?>
<?php require_once __DIR__ . "/../../layouts/sidebar.php"; ?>

<h2>Upload tài liệu bài học</h2>

<form action="/materials/upload" method="POST" enctype="multipart/form-data">
    <label>ID bài học</label>
    <input type="number" name="lesson_id" required>

    <label>Chọn file</label>
    <input type="file" name="material" accept=".pdf,.doc,.ppt,.pptx" required>

    <button type="submit">Upload</button>
</form>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
