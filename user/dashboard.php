<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$user = get_user_data($user_id);
if (!$user) {
    header("Location: ../logout.php");
    exit;
}

$page_title = "Dashboard";
require_once '../includes/header.php';

// Ambil data mood 7 hari terakhir
$mood_data = [];
$mood_labels = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = $conn->prepare("SELECT mood FROM mood_tracker WHERE user_id = ? AND date = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $mood_labels[] = date('D', strtotime($date));
    $row = $result->fetch_assoc();
    $mood_data[] = $row ? $row['mood'] : null;
}

// Ambil konsultasi mendatang
$upcoming_consultations = [];
$stmt = $conn->prepare("SELECT c.*, u.username AS counselor_name 
FROM consultations c
JOIN counselors co ON c.counselor_id = co.counselor_id
JOIN users u ON co.user_id = u.user_id
WHERE c.user_id = ? 
AND (c.status = 'confirmed' OR c.status = 'completed')
AND c.schedule > NOW()
ORDER BY c.schedule ASC 
LIMIT 3");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcoming_consultations[] = $row;
}

// Ambil post forum terakhir
$recent_posts = [];
$stmt = $conn->prepare("SELECT p.*, COUNT(r.reply_id) AS replies 
                       FROM forum_posts p
                       LEFT JOIN forum_replies r ON p.post_id = r.post_id
                       WHERE p.user_id = ?
                       GROUP BY p.post_id
                       ORDER BY p.created_at DESC LIMIT 3");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_posts[] = $row;
}

// Ambil hasil tes terakhir
$test_results = [];
$stmt = $conn->prepare("SELECT * FROM test_results 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC LIMIT 3");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $test_results[] = $row;
}

// Ambil konsultasi terakhir yang belum diberi rating
$unrated_consultation = null;
$stmt = $conn->prepare("SELECT * FROM consultations 
                        WHERE user_id = ? AND status = 'completed' AND rating IS NULL 
                        ORDER BY schedule DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$unrated_consultation = $result->fetch_assoc();
?>
<style>
    /* === Konsultasi Mendatang === */
    .dashboard-card.consultations {
        background: linear-gradient(135deg, #e3f2fd, #ffffff);
        border-left: 6px solid #42a5f5;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 14px rgba(66, 165, 245, 0.15);
    }

    .consultation-item {
        background-color: #f5fbff;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #cbe7fc;
        transition: transform 0.2s ease-in-out;
    }

    .consultation-item:hover {
        transform: translateY(-2px);
    }

    .rate-button {
    display: inline-block;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #ff9800, #ffc107);
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);
    text-decoration: none;
    transition: all 0.3s ease;
}

.rate-button:hover {
    background: linear-gradient(135deg, #fb8c00, #ffa000);
    box-shadow: 0 6px 14px rgba(255, 152, 0, 0.5);
    transform: translateY(-2px);
}


    /* === Aktivitas Forum === */
    .dashboard-card.forum-activity {
        background: linear-gradient(135deg, #ede7f6, #ffffff);
        border-left: 6px solid #7e57c2;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 14px rgba(126, 87, 194, 0.15);
    }

    .post-item {
        background-color: #f4f0fb;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #d6c7f2;
        margin-bottom: 12px;
        transition: transform 0.2s ease-in-out;
    }

    .post-item:hover {
        transform: translateY(-2px);
    }

    /* === Hasil Tes Terakhir === */
    .dashboard-card.test-results {
        background: linear-gradient(135deg, #e8f5e9, #ffffff);
        border-left: 6px solid #66bb6a;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 14px rgba(102, 187, 106, 0.15);
    }

    .test-item {
        background-color: #f0fdf4;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #b2dfdb;
        margin-bottom: 12px;
        transition: transform 0.2s ease-in-out;
    }

    .test-item:hover {
        transform: translateY(-2px);
    }

    .severity-badge {
        font-size: 0.85em;
        padding: 6px 10px;
        border-radius: 8px;
        margin-left: 8px;
        text-transform: capitalize;
    }

    .severity-badge.rendah {
        background-color: #66bb6a;
        color: #fff;
    }

    .severity-badge.sedang {
        background-color: #ffa726;
        color: #fff;
    }

    .severity-badge.tinggi {
        background-color: #ef5350;
        color: #fff;
    }
</style>
<section class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Selamat Datang, <?php echo $user['is_anonymous'] ? 'Teman' : htmlspecialchars($user['username']); ?>!</h1>
            <div class="quick-actions">
                <a href="<?php echo BASE_URL; ?>/tests/phq9.php" class="btn btn-primary">
                    <i class="fas fa-clipboard-check"></i> Tes Mental
                </a>
                <a href="<?php echo BASE_URL; ?>/forum/create.php" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Buat Post
                </a>
                <a href="<?php echo BASE_URL; ?>/consultation" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> konsultasi
                </a>
                <!-- <a href="daftar_chat.php">
                    <button style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px;">📋 Daftar Chat</button>
                </a> -->

                <?php if ($unrated_consultation): ?>
                    <a href="rate_consultation.php?id=<?= $unrated_consultation['consultation_id'] ?>" class="rate-button">
                        ⭐ Beri Rating untuk Sesi Terakhir
                    </a>
                <?php endif; ?>

            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Mood Tracking Section -->
            <div class="dashboard-card mood-tracker">
                <h3><i class="fas fa-smile-beam"></i> Mood Tracker</h3>
                <div class="mood-chart">
                    <canvas id="moodChart"></canvas>
                </div>
                <a href="<?php echo BASE_URL; ?>/user/mood-tracker.php" class="btn btn-secondary">
                    Lihat Detail <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Upcoming Consultations -->
            <div class="dashboard-card consultations">
                <h3><i class="fas fa-calendar-alt"></i> Konsultasi Mendatang</h3>
                <?php if (!empty($upcoming_consultations)): ?>
                    <div class="consultation-list">
                        <?php foreach ($upcoming_consultations as $consultation): ?>
                            <div class="consultation-item">
                                <div class="consultation-info">
                                    <div class="counselor-name">
                                        <strong><?php echo htmlspecialchars($consultation['counselor_name']); ?></strong>
                                    </div>
                                    <div class="consultation-time text-muted">
                                        <i class="fas fa-clock"></i> <?= date('D, d M Y H:i', strtotime($consultation['schedule'])); ?>
                                    </div>
                                    <div class="consultation-type badge badge-info">
                                        <?= $consultation['duration']; ?> menit via
                                        <?= isset($consultation['notes']) && strpos($consultation['notes'], 'zoom') !== false ? '<i class="fab fa-zoom"></i> Zoom' : '<i class="fas fa-video"></i> Daring'; ?>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>Tidak ada konsultasi mendatang</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Forum Posts -->
            <div class="dashboard-card forum-activity">
                <h3><i class="fas fa-comments"></i> Aktivitas Forum Terakhir</h3>
                <?php if (!empty($recent_posts)): ?>
                    <div class="post-list">
                        <?php foreach ($recent_posts as $post): ?>
                            <div class="post-item">
                                <div class="post-title">
                                    <a href="<?php echo BASE_URL; ?>/forum/post.php?id=<?php echo $post['post_id']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </div>
                                <div class="post-meta">
                                    <span class="post-date">
                                        <?php echo time_ago($post['created_at']); ?>
                                    </span>
                                    <span class="post-replies">
                                        <i class="fas fa-comment"></i> <?php echo $post['replies']; ?>
                                    </span>
                                </div>
                                <?php if ($post['user_id'] == $user_id): ?>
                                    <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus post ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-comment-slash"></i>
                        <p>Belum ada aktivitas di forum</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Test Results -->
            <div class="dashboard-card test-results">
                <h3><i class="fas fa-clipboard-list"></i> Hasil Tes Terakhir</h3>
                <?php if (!empty($test_results)): ?>
                    <div class="test-list">
                        <?php foreach ($test_results as $test): ?>
                            <div class="test-item">
                                <div class="test-type">
                                    <?php echo htmlspecialchars($test['test_type']); ?>
                                </div>
                                <div class="test-score">
                                    Skor: <?php echo $test['score']; ?>
                                    <span class="severity-badge <?php echo htmlspecialchars($test['severity']); ?>">
                                        <?php echo ucfirst($test['severity']); ?>
                                    </span>
                                </div>
                                <div class="test-date">
                                    <?php echo time_ago($test['created_at']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-medical-alt"></i>
                        <p>Belum ada hasil tes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const moodData = {
        labels: <?php echo json_encode($mood_labels); ?>,
        data: <?php echo json_encode($mood_data); ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('moodChart').getContext('2d');
        const moodChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: moodData.labels,
                datasets: [{
                    label: 'Mood Minggu Ini',
                    data: moodData.data.map(mood => {
                        const moodValues = {
                            'sangat_bahagia': 5,
                            'bahagia': 4,
                            'netral': 3,
                            'sedih': 2,
                            'sangat_sedih': 1,
                            'marah': 2,
                            'cemas': 2
                        };
                        return moodValues[mood] || null;
                    }),
                    backgroundColor: 'rgba(108, 99, 255, 0.1)',
                    borderColor: 'rgba(108, 99, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                const moods = {
                                    5: 'Sangat Bahagia',
                                    4: 'Bahagia',
                                    3: 'Netral',
                                    2: 'Kurang Baik',
                                    1: 'Sangat Buruk'
                                };
                                return moods[value] || '';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Mood: ' + (moodData.data[context.dataIndex] || 'Tidak ada data');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>