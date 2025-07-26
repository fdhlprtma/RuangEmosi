<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();
require_counselor();

// Proses aksi hapus
if (isset($_GET['delete'])) {
    $article_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM articles WHERE article_id = $article_id");
    redirect('manage-articles.php', 'Artikel berhasil dihapus');
}

// Proses ubah status publikasi
if (isset($_GET['toggle_publish'])) {
    $article_id = (int)$_GET['toggle_publish'];
    $conn->query("UPDATE articles SET is_published = 1 - is_published WHERE article_id = $article_id");
    redirect('manage-articles.php', 'Status artikel diperbarui');
}

// Ambil semua artikel
$articles = $conn->query("
    SELECT a.*, u.username AS author 
    FROM articles a
    JOIN users u ON a.author_id = u.user_id
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Kelola Artikel";
require_once '../includes/header.php';
?>

<section class="manage-articles">
    <div class="container">
        <h1>Kelola Artikel</h1>

        <a href="create-article.php" class="btn btn-primary" style="margin-bottom: 20px;">
            <i class="fas fa-plus"></i> Tambah Artikel Baru
        </a>

        <div class="article-list">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <div class="article-info">
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <p><small><?= $article['category'] ?> | <?= $article['author'] ?> | <?= $article['created_at'] ?></small></p>
                        <p><?= htmlspecialchars($article['excerpt']) ?></p>
                    </div>
                    <div class="article-actions">
                        <a href="edit-article.php?id=<?= $article['article_id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?toggle_publish=<?= $article['article_id'] ?>" class="btn btn-sm <?= $article['is_published'] ? 'btn-secondary' : 'btn-success' ?>">
                            <i class="fas fa-eye<?= $article['is_published'] ? '-slash' : '' ?>"></i> 
                            <?= $article['is_published'] ? 'Sembunyikan' : 'Publikasikan' ?>
                        </a>
                        <a href="?delete=<?= $article['article_id'] ?>" onclick="return confirm('Yakin ingin menghapus artikel ini?')" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.manage-articles {
    padding: 40px 0;
}

.article-card {
    background: white;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.article-info h3 {
    margin-top: 0;
    margin-bottom: 5px;
}

.article-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 150px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.9rem;
}
</style>

<?php require_once '../includes/footer.php'; ?>
