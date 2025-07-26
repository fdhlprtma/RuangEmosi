<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$page_title = "Forum Curhat";
require_once '../includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filter kategori
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

// Total posts query
if ($category === 'all') {
  $total_query = "SELECT COUNT(*) FROM forum_posts";
} else {
  $total_query = "SELECT COUNT(*) FROM forum_posts WHERE category = '$category'";
}
$total_result = $conn->query($total_query);
$total_posts = $total_result->fetch_row()[0];
$total_pages = ceil($total_posts / $per_page);

// Post data query
if ($category === 'all') {
  $query = "SELECT p.*, u.username 
              FROM forum_posts p 
              LEFT JOIN users u ON p.user_id = u.user_id 
              ORDER BY p.created_at DESC 
              LIMIT $offset, $per_page";
} else {
  $query = "SELECT p.*, u.username 
              FROM forum_posts p 
              LEFT JOIN users u ON p.user_id = u.user_id 
              WHERE p.category = '$category' 
              ORDER BY p.created_at DESC 
              LIMIT $offset, $per_page";
}
$result = $conn->query($query);

// Helper function
function time_ago($datetime)
{
  $time = strtotime($datetime);
  $diff = time() - $time;

  if ($diff < 60) return $diff . ' seconds ago';
  elseif ($diff < 3600) return floor($diff / 60) . ' minutes ago';
  elseif ($diff < 86400) return floor($diff / 3600) . ' hours ago';
  else return floor($diff / 86400) . ' days ago';
}
?>

<section class="forum-container">
  <div class="container">
    <div class="forum-header">
      <h2>Forum Curhat</h2>
      <p>Bagikan perasaanmu atau beri dukungan kepada orang lain</p>

      <?php if (is_logged_in()): ?>
        <a href="<?php echo BASE_URL; ?>/forum/create.php" class="btn btn-primary">Buat Post Baru</a>
      <?php else: ?>
        <p><a href="<?php echo BASE_URL; ?>/login.php">Login</a> untuk membuat post atau memberikan komentar.</p>
      <?php endif; ?>
    </div>

    <div class="forum-filters">
      <div class="filter-category">
        <!-- <span>Filter Kategori:</span> -->
        <a href="?category=all" class="<?php echo $category === 'all' ? 'active' : ''; ?>">Semua</a>
        <a href="?category=curhat" class="<?php echo $category === 'curhat' ? 'active' : ''; ?>">Curhat</a>
        <a href="?category=tanya" class="<?php echo $category === 'tanya' ? 'active' : ''; ?>">Tanya</a>
        <a href="?category=dukungan" class="<?php echo $category === 'dukungan' ? 'active' : ''; ?>">Dukungan</a>
        <a href="?category=cerita" class="<?php echo $category === 'cerita' ? 'active' : ''; ?>">Cerita</a>
      </div>
    </div>

    <div class="forum-posts">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($post = $result->fetch_assoc()): ?>
          <div class="post-card">
            <div class="post-header">
              <div class="post-author">
                <?php if ($post['is_anonymous'] && $post['user_id'] != ($_SESSION['user_id'] ?? 0)): ?>
                  <span class="anonymous-badge"><i class="fas fa-user-secret"></i> Anonim</span>
                <?php else: ?>
                  <span><?php echo htmlspecialchars($post['username']); ?></span>
                <?php endif; ?>
              </div>
              <div class="post-category">
                <?php
                $category_labels = [
                  'curhat' => 'Curhat',
                  'tanya' => 'Tanya',
                  'dukungan' => 'Dukungan',
                  'cerita' => 'Cerita'
                ];
                echo $category_labels[$post['category']] ?? 'Umum';
                ?>
              </div>
            </div>

            <div class="post-content">
              <h3><a href="<?php echo BASE_URL; ?>/forum/post.php?id=<?php echo $post['post_id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
              <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?><?php echo strlen($post['content']) > 200 ? '...' : ''; ?></p>
            </div>

            <div class="post-footer">
              <div class="post-meta">
              <span><i class="far fa-clock"></i> <?php echo time_ago($post['created_at']); ?></span>
              <span><i class="far fa-comment"></i> <?php echo $post['reply_count']; ?> komentar</span>
              <span><i class="far fa-eye"></i> <?php echo $post['view_count']; ?> dilihat</span>
              </div>
              <?php if (is_admin()): ?>
              <form method="post" action="delete_post.php" onsubmit="return confirm('Yakin ingin menghapus post ini?');" style="display:inline; margin: 0;">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <button type="submit" class="btn btn-danger" style="margin-right: 10px;"><i class="fas fa-trash-alt"></i> Hapus</button>
                <a href="<?php echo BASE_URL; ?>/forum/post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-secondary"><i class="fas fa-book-open"></i> Baca Selengkapnya</a>
              </form>
              <?php endif; ?>
              <?php if (get_logged_in_user_id() && !is_admin()): ?>
                <a href="<?php echo BASE_URL; ?>/forum/post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-secondary"><i class="fas fa-book-open"></i> Baca Selengkapnya</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="far fa-comment-dots"></i>
          <p>Belum ada post di kategori ini. Jadilah yang pertama untuk berbagi!</p>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $category; ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $category; ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<style>
  :root {
    --primary-color:rgb(19, 69, 149);
    --secondary-color:rgb(40, 109, 198);
    --accent-color: #c8d8e4;
    --text-color: #2b2b2b;
    --background-color: #f8f9fa;
}

.forum-container {
    padding: 2rem 0;
    background-color: var(--background-color);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.forum-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.forum-header h2 {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-weight: 700;
}

.forum-header p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 103, 119, 0.2);
}

.forum-filters {
    margin-bottom: 2rem;
    display: flex;
    justify-content: center;
}

.filter-category {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-category a {
    padding: 0.6rem 1.2rem;
    border-radius: 20px;
    background-color: var(--accent-color);
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.filter-category a.active,
.filter-category a:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--secondary-color);
}

.forum-posts {
    display: grid;
    gap: 1.5rem;
}

.post-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid var(--primary-color);
}

.post-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.post-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.anonymous-badge {
    background-color: var(--accent-color);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.post-category {
    background-color: var(--secondary-color);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.post-content h3 {
    margin-bottom: 0.8rem;
    font-size: 1.4rem;
}

.post-content h3 a {
    color: var(--text-color);
    text-decoration: none;
    transition: color 0.2s;
}

.post-content h3 a:hover {
    color: var(--primary-color);
}

.post-content p {
    color: #555;
    line-height: 1.7;
    margin-bottom: 1rem;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.post-meta {
    display: flex;
    gap: 1.5rem;
    color: #666;
    font-size: 0.9rem;
}

.post-meta i {
    margin-right: 0.3rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.empty-state i {
    font-size: 3rem;
    color: var(--accent-color);
    margin-bottom: 1rem;
}

.empty-state p {
    color: #666;
    font-size: 1.1rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 3rem;
}

.pagination a {
    padding: 0.6rem 1rem;
    border-radius: 8px;
    background-color: white;
    color: var(--text-color);
    text-decoration: none;
    border: 1px solid #ddd;
    transition: all 0.2s;
}

.pagination a.active,
.pagination a:hover {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.btn-danger {
    background-color: #e74c3c;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    color: white;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s;
}

.btn-danger:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: var(--secondary-color);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    color: white;
    background-color:rgb(14, 114, 181);
}

@media (max-width: 768px) {
    .forum-header h2 {
        font-size: 2rem;
    }
    
    .post-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .post-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .post-meta {
        flex-wrap: wrap;
        gap: 1rem;
    }
}
  .btn-danger {
    background-color: #e74c3c;
    color: white;
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
  }

  .btn-danger:hover {
    background-color: #c0392b;
  }
</style>

<?php require_once '../includes/footer.php'; ?>
