<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

if (!isset($_GET['id'])) {
    redirect('manage-articles.php', 'ID artikel tidak ditemukan.');
}

$article_id = (int) $_GET['id'];

// Ambil data artikel
$stmt = $conn->prepare("SELECT * FROM articles WHERE article_id = ?");
$stmt->bind_param('i', $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();

if (!$article) {
    redirect('manage-articles.php', 'Artikel tidak ditemukan.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $slug        = trim($_POST['slug']);
    $excerpt     = trim($_POST['excerpt']);
    $content     = trim($_POST['content']);
    $category    = $_POST['category'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $featured_image = $article['featured_image'];

    // Upload gambar jika ada yang baru
    if (!empty($_FILES['featured_image']['name'])) {
        $upload_dir = '../assets/images/articles/';
        $filename = uniqid() . '_' . basename($_FILES['featured_image']['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_path)) {
            $featured_image = $filename;
        } else {
            $errors[] = 'Gagal mengupload gambar.';
        }
    }

    // Cek slug unik kecuali slug milik artikel ini
    $stmt = $conn->prepare("SELECT COUNT(*) FROM articles WHERE slug = ? AND article_id != ?");
    $stmt->bind_param('si', $slug, $article_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $errors[] = 'Slug sudah digunakan oleh artikel lain.';
    }

    if (!$errors) {
        // Perbarui artikel
        $stmt = $conn->prepare("UPDATE articles SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, category = ?, is_published = ?, updated_at = NOW() WHERE article_id = ?");
        $stmt->bind_param('ssssssii', $title, $slug, $excerpt, $content, $featured_image, $category, $is_published, $article_id);
        $stmt->execute();
        $stmt->close();

        // Tambahkan log system
        $user_id = $_SESSION['user_id']; // Ambil ID user yang sedang login
        $action = 'Edit Artikel';
        $description = "Artikel dengan ID {$article_id} telah diedit.";

        $log_stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, description) VALUES (?, ?, ?)");
        $log_stmt->bind_param('iss', $user_id, $action, $description);
        $log_stmt->execute();
        $log_stmt->close();

        redirect('manage-articles.php', 'Artikel berhasil diperbarui.');
    }
}

$page_title = "Edit Artikel";
require_once '../includes/header.php';
?>

<section class="edit-article">
    <div class="container">
        <h1>Edit Artikel</h1>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="article-form">
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
            </div>

            <div class="form-group">
                <label>Slug (URL)</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($article['slug']) ?>" required>
            </div>

            <div class="form-group">
                <label>Excerpt</label>
                <textarea name="excerpt" rows="3"><?= htmlspecialchars($article['excerpt']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Konten Artikel</label>
                <textarea name="content" rows="8" required><?= htmlspecialchars($article['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Saat Ini</label><br>
                <?php if ($article['featured_image']): ?>
                    <img src="../assets/images/articles/<?= htmlspecialchars($article['featured_image']) ?>" alt="Gambar" style="max-width: 200px;">
                <?php else: ?>
                    <p><em>Tidak ada gambar</em></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ganti Gambar</label>
                <input type="file" name="featured_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="category" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    $categories = ['depresi', 'kecemasan', 'self-care', 'stres', 'relationship'];
                    foreach ($categories as $cat) {
                        $selected = $article['category'] === $cat ? 'selected' : '';
                        echo "<option value=\"$cat\" $selected>$cat</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="is_published" <?= $article['is_published'] ? 'checked' : '' ?>> Publikasi</label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</section>

<style>
.edit-article {
    padding: 40px 0;
}

.article-form {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.article-form .form-group {
    margin-bottom: 20px;
}

.article-form label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

.article-form input[type="text"],
.article-form textarea,
.article-form select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
</style>

<?php require_once '../includes/footer.php'; ?>
