<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$page_title = "Artikel Kesehatan Mental";
require_once '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Filter kategori
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

// Query untuk total artikel
if ($category === 'all') {
    $total_query = "SELECT COUNT(*) FROM articles WHERE is_published = TRUE";
} else {
    $total_query = "SELECT COUNT(*) FROM articles WHERE category = '$category' AND is_published = TRUE";
}
$total_result = $conn->query($total_query);
$total_articles = $total_result->fetch_row()[0];
$total_pages = ceil($total_articles / $per_page);

// Query untuk artikel
if ($category === 'all') {
    $query = "SELECT a.*, u.username 
              FROM articles a 
              JOIN users u ON a.author_id = u.user_id
              WHERE a.is_published = TRUE
              ORDER BY a.created_at DESC 
              LIMIT $offset, $per_page";
} else {
    $query = "SELECT a.*, u.username 
              FROM articles a 
              JOIN users u ON a.author_id = u.user_id
              WHERE a.category = '$category' AND a.is_published = TRUE
              ORDER BY a.created_at DESC 
              LIMIT $offset, $per_page";
}

$result = $conn->query($query);
?>

<section class="articles">
    <div class="container">
        <div class="articles-header">
            <h2>Artikel Kesehatan Mental</h2>
            <p>Temukan informasi dan tips untuk kesehatan mental yang lebih baik</p>
        </div>

        <div class="articles-filters">
            <div class="filter-category">
                <span>Filter Kategori:</span>
                <a href="?category=all" class="<?php echo $category === 'all' ? 'active' : ''; ?>">Semua</a>
                <a href="?category=depresi" class="<?php echo $category === 'depresi' ? 'active' : ''; ?>">Depresi</a>
                <a href="?category=kecemasan" class="<?php echo $category === 'kecemasan' ? 'active' : ''; ?>">Kecemasan</a>
                <a href="?category=self-care" class="<?php echo $category === 'self-care' ? 'active' : ''; ?>">Self-Care</a>
                <a href="?category=stres" class="<?php echo $category === 'stres' ? 'active' : ''; ?>">Stres</a>
                <a href="?category=relationship" class="<?php echo $category === 'relationship' ? 'active' : ''; ?>">Relationship</a>
            </div>

            <?php if (is_logged_in() && (is_admin() || is_counselor())): ?>
                <a href="<?php echo BASE_URL; ?>/admin/manage-articles.php" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Kelola Artikel
                </a>
            <?php endif; ?>
        </div>

        <div class="articles-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($article = $result->fetch_assoc()): ?>
                    <div class="article-card">
                        <div class="article-image">
                            <img src="<?php echo $article['featured_image'] ? 
                                      BASE_URL . '/assets/images/articles/' . $article['featured_image'] : 
                                      BASE_URL . '/assets/images/default-article.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                        <div class="article-content">
                            <div class="article-category">
                                <?php 
                                $category_labels = [
                                    'depresi' => 'Depresi',
                                    'kecemasan' => 'Kecemasan',
                                    'self-care' => 'Self-Care',
                                    'stres' => 'Stres',
                                    'relationship' => 'Relationship'
                                ];
                                echo $category_labels[$article['category']] ?? 'Umum'; 
                                ?>
                            </div>
                            <h3>
                                <a href="<?php echo BASE_URL; ?>/articles/view.php?id=<?php echo $article['article_id']; ?>">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                            </h3>
                            <p class="article-excerpt">
                                <?php echo htmlspecialchars($article['excerpt'] ?: substr($article['content'], 0, 150) . '...'); ?>
                            </p>
                            <div class="article-meta">
                                <span class="article-author">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($article['username']); ?>
                                </span>
                                <span class="article-date">
                                    <i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-newspaper"></i>
                    <p>Belum ada artikel di kategori ini</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $category; ?>">
                        <i class="fas fa-chevron-left"></i> Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>" 
                       class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $category; ?>">
                        Selanjutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Style untuk artikel */
.articles {
    padding: 50px 0;
    background-color: #f9f9f9;
}

.articles-header {
    text-align: center;
    margin-bottom: 30px;
}

.articles-header h2 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 10px;
}

.articles-header p {
    font-size: 1.1rem;
    color: #555;
}

.articles-filters {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filter-category a {
    font-size: 1rem;
    color: #007bff;
    margin-right: 15px;
    text-decoration: none;
}

.filter-category a.active {
    font-weight: bold;
    color: #0056b3;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
}

.article-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s;
}

.article-card:hover {
    transform: translateY(-10px);
}

.article-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-bottom: 4px solid #f0f0f0;
}

.article-content {
    padding: 20px;
}

.article-category {
    font-size: 1rem;
    color: #007bff;
    margin-bottom: 10px;
}

.article-content h3 a {
    font-size: 1.5rem;
    color: #333;
    text-decoration: none;
}

.article-content h3 a:hover {
    color: #007bff;
}

.article-excerpt {
    font-size: 1rem;
    color: #555;
    margin-bottom: 20px;
}

.article-meta {
    font-size: 0.9rem;
    color: #777;
}

.article-meta i {
    margin-right: 5px;
}

.pagination {
    text-align: center;
    margin-top: 40px;
}

.pagination a {
    padding: 10px 15px;
    color: #007bff;
    text-decoration: none;
    font-size: 1rem;
    border: 1px solid #007bff;
    margin: 0 5px;
    border-radius: 5px;
}

.pagination a.active {
    background-color: #007bff;
    color: #fff;
}

.pagination a:hover {
    background-color: #0056b3;
    color: #fff;
}.articles {
    padding: 60px 0;
    background-color: #f8fbff;
}

.articles-header h2 {
    font-size: 2.8rem;
    color: #1f3b57;
    font-weight: 700;
}

.articles-header p {
    font-size: 1.2rem;
    color: #6c757d;
}

.articles-filters {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 20px 0 30px;
}

.filter-category {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-category a {
    background-color: #e9f1fa;
    color: #007bff;
    padding: 8px 14px;
    border-radius: 20px;
    text-decoration: none;
    transition: 0.3s;
}

.filter-category a.active,
.filter-category a:hover {
    background-color: #007bff;
    color: white;
}

.btn.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    font-weight: 600;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.article-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.article-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.article-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.article-category {
    font-size: 0.85rem;
    font-weight: 500;
    color: #fff;
    background-color: #007bff;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
    margin-bottom: 10px;
}

.article-content h3 a {
    font-size: 1.4rem;
    color: #1f3b57;
    text-decoration: none;
    line-height: 1.3;
    margin-bottom: 10px;
    display: inline-block;
}

.article-content h3 a:hover {
    color: #007bff;
}

.article-excerpt {
    font-size: 0.95rem;
    color: #5a5a5a;
    margin-bottom: 15px;
    flex-grow: 1;
}

.article-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #6c757d;
}

.article-meta i {
    margin-right: 5px;
}

.pagination {
    text-align: center;
    margin-top: 40px;
}

.pagination a {
    padding: 10px 14px;
    color: #007bff;
    text-decoration: none;
    border: 1px solid #007bff;
    margin: 0 4px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pagination a.active,
.pagination a:hover {
    background-color: #007bff;
    color: #fff;
}

</style>

<?php require_once '../includes/footer.php'; ?>
