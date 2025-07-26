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
$recent_activities = $conn->query("SELECT 'user' AS type, user_id AS id, username AS title, created_at 
                                   FROM users 
                                   ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Ambil statistik forum
$forum_stats = $conn->query("SELECT COUNT(*) FROM forum_posts WHERE created_at >= CURDATE() - INTERVAL 30 DAY")->fetch_row()[0];

$page_title = "Laporan Admin";
require_once '../includes/header.php';
?>

<section class="admin-report">
    <div class="container">
        <h1>Laporan Admin</h1>

        <div class="report-grid">
            <div class="report-card">
                <h3>Total Pengguna</h3>
                <div class="report-value"><?= $stats['total_users'] ?></div>
            </div>
            <div class="report-card">
                <h3>Pengguna Baru (7 Hari)</h3>
                <div class="report-value"><?= $stats['new_users'] ?></div>
            </div>
            <div class="report-card">
                <h3>Total Post Forum</h3>
                <div class="report-value"><?= $stats['total_posts'] ?></div>
            </div>
            <div class="report-card">
                <h3>Konsultasi Aktif</h3>
                <div class="report-value"><?= $stats['active_consultations'] ?></div>
            </div>
        </div>

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
                            <span class="activity-time"><?= time_ago($activity['created_at']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="forum-stats">
            <h2>Statistik Forum (30 Hari Terakhir)</h2>
            <div class="forum-stat-card">
                <h3>Total Post Forum</h3>
                <div class="forum-stat-value"><?= $forum_stats ?></div>
            </div>
        </div>
    </div>
</section>

<style>
    .admin-report {
        padding: 40px 0;
    }

    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .report-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .report-card h3 {
        color: var(--gray-color);
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .report-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    .recent-activities, .forum-stats {
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

    .forum-stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .forum-stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }
</style>

<?php require_once '../includes/footer.php'; ?>
