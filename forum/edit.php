<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!is_logged_in()) {
    redirect(BASE_URL . '/login.php', 'Silakan login terlebih dahulu');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . '/forum', 'Post tidak valid');
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data post yang akan diedit
$stmt = $conn->prepare("SELECT * FROM forum_posts WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

// Validasi kepemilikan post
if (!$post || (!is_admin() && $post['user_id'] != $user_id)) {
    redirect(BASE_URL . '/forum', 'Anda tidak memiliki akses untuk mengedit post ini');
}

// Proses form update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category = sanitize($_POST['category']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE forum_posts 
                          SET title = ?, content = ?, category = ?, is_anonymous = ?, updated_at = NOW()
                          WHERE post_id = ?");
    $stmt->bind_param("ssssi", $title, $content, $category, $is_anonymous, $post_id);
    
    if ($stmt->execute()) {
        redirect(BASE_URL . "/forum/post.php?id=$post_id", 'Post berhasil diperbarui');
    } else {
        $error = 'Gagal memperbarui post';
    }
}

$page_title = "Edit Post: " . htmlspecialchars($post['title']);
?>

<section class="mindspace-post-container">
    <div class="emotion-container">
        <div class="post-navigation">
            <a href="<?= BASE_URL ?>/forum/post.php?id=<?= $post_id ?>" class="emotion-btn emotion-btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Post
            </a>
        </div>

        <div class="mindspace-edit-card">
            <h1 class="edit-title"><i class="fas fa-edit"></i> Edit Post</h1>
            
            <?php display_message(); ?>
            
            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="title">Judul</label>
                    <input type="text" id="title" name="title" 
                           value="<?= htmlspecialchars($post['title']) ?>" 
                           required class="form-input">
                </div>

                <div class="form-group">
                    <label for="content">Konten</label>
                    <textarea id="content" name="content" rows="6" 
                              required class="form-textarea"><?= 
                              htmlspecialchars($post['content']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" class="form-select">
                        <option value="curhat" <?= $post['category'] === 'curhat' ? 'selected' : '' ?>>Curhat</option>
                        <option value="tanya" <?= $post['category'] === 'tanya' ? 'selected' : '' ?>>Tanya</option>
                        <option value="dukungan" <?= $post['category'] === 'dukungan' ? 'selected' : '' ?>>Dukungan</option>
                        <option value="cerita" <?= $post['category'] === 'cerita' ? 'selected' : '' ?>>Cerita</option>
                    </select>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_anonymous" name="is_anonymous" 
                           <?= $post['is_anonymous'] ? 'checked' : '' ?>>
                    <label for="is_anonymous">Posting sebagai Anonim</label>
                </div>

                <div class="form-actions">
                    <button type="submit" classemotion-btn emotion-btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.mindspace-edit-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    padding: 2rem;
    margin-top: 1.5rem;
}

.edit-title {
    font-size: 1.8rem;
    color: #1e293b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.edit-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #334155;
}

.form-input {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #7dd3fc;
    box-shadow: 0 0 0 3px rgba(125, 211, 252, 0.2);
}

.form-textarea {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    min-height: 200px;
    resize: vertical;
    line-height: 1.6;
}

.form-select {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background-color: white;
    font-size: 1rem;
    max-width: 300px;
}

.checkbox-group {
    flex-direction: row;
    align-items: center;
    gap: 0.75rem;
}

.form-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

@media (max-width: 640px) {
    .mindspace-edit-card {
        padding: 1.5rem;
    }
    
    .edit-title {
        font-size: 1.5rem;
    }
    
    .form-select {
        max-width: 100%;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>