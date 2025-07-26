<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

// Ambil data statistik
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'new_users' => $conn->query("SELECT COUNT(*) FROM users WHERE created_at >= CURDATE() - INTERVAL 7 DAY")->fetch_row()[0],
    'total_posts' => $conn->query("SELECT COUNT(*) FROM forum_posts")->fetch_row()[0],
    'active_consultations' => $conn->query("SELECT COUNT(*) FROM consultations WHERE status = 'confirmed'")->fetch_row()[0]
];

// Ambil aktivitas terbaru
$recent_activities = $conn->query("
    SELECT 'user' AS type, user_id AS id, username AS title, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Dashboard Admin";
require_once '../includes/header.php';
?>

<section class="admin-dashboard">
    <div class="container">
        <h1>Dashboard Admin</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Pengguna</h3>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Pengguna Baru (7 Hari)</h3>
                <div class="stat-value"><?= $stats['new_users'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Post Forum</h3>
                <div class="stat-value"><?= $stats['total_posts'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Konsultasi Aktif</h3>
                <div class="stat-value"><?= $stats['active_consultations'] ?></div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="recent-activities">
                <h2>Aktivitas Terbaru</h2>
                <div class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <?php if ($activity['type'] == 'user'): ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div class="activity-info">
                                <h4><?= htmlspecialchars($activity['title']) ?></h4>
                                <span class="activity-time">
                                    <?= time_ago($activity['created_at']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-grid">
                    <a href="manage-users.php" class="action-card">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                    <a href="manage-articles.php" class="action-card">
                        <i class="fas fa-file-alt"></i>
                        <span>Kelola Artikel</span>
                    </a>
                    <a href="system-logs.php" class="action-card">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Log Sistem</span>
                    </a>
                    <a href="reports.php" class="action-card">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                    <!-- Tambahkan ini untuk approve-counselor.php -->
                    <a href="approve-counselor.php" class="action-card">
                        <i class="fas fa-user-check"></i>
                        <span>Approve Konselor</span>
                    </a>
                    <a href="manage-counselor.php" class="action-card">
                        <i class="fas fa-user-graduate"></i>
                        <span>Kelola Konselor</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .admin-dashboard {
        padding: 40px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .stat-card h3 {
        color: var(--gray-color);
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    .dashboard-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 40px;
    }

    .recent-activities {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .activity-icon {
        font-size: 1.5rem;
        margin-right: 15px;
        color: var(--primary-color);
    }

    .quick-actions .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .action-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        transition: transform 0.2s;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .action-card:hover {
        transform: translateY(-5px);
    }

    .action-card i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>