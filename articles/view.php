<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = "Lihat Artikel";
require_once '../includes/header.php';

if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];

    // Query untuk mengambil artikel berdasarkan ID
    $query = "SELECT a.*, u.username FROM articles a JOIN users u ON a.author_id = u.user_id WHERE a.article_id = $article_id AND a.is_published = TRUE";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
    } else {
        echo "<p>Artikel tidak ditemukan.</p>";
        exit;
    }
}
?>

<section class="article-view">
    <div class="container">
        <div class="article-header">
            <h1><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <span class="article-author">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($article['username']); ?>
                </span>
                <span class="article-date">
                    <i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                </span>
            </div>
        </div>

        <div class="article-content">
            <div class="article-image">
                <img src="<?php echo $article['featured_image'] ? 
                          BASE_URL . '/assets/images/articles/' . $article['featured_image'] : 
                          BASE_URL . '/assets/images/default-article.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($article['title']); ?>">
            </div>
            <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
        </div>
    </div>
</section>

<style>
.article-view .article-image img {
    width: 100%;
    max-width: 600px;  /* Gambar membesar menjadi 600px di halaman view */
    height: auto;
    margin: 0 auto 20px; /* Pusatkan gambar */
    display: block;
    border-radius: 8px;
}
</style>

<?php require_once '../includes/footer.php'; ?>
