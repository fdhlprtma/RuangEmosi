<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data konselor
$stmt = $conn->prepare("SELECT * FROM counselors WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$counselor = $stmt->get_result()->fetch_assoc();

// Ambil jadwal konsultasi
$stmt = $conn->prepare("
    SELECT c.*, u.username, u.profile_pic 
    FROM consultations c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.counselor_id = ?
    AND c.schedule >= CURDATE()
    ORDER BY c.schedule ASC
    LIMIT 5
");
$stmt->bind_param("i", $counselor['counselor_id']);
$stmt->execute();
$consultations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$counselor_id = $_SESSION['counselor_id']; // atau dari relasi user_id

$stmt = $conn->prepare("SELECT c.*, u.username AS user_name 
                        FROM consultations c
                        JOIN users u ON c.user_id = u.user_id
                        WHERE c.counselor_id = ? AND c.rating IS NOT NULL
                        ORDER BY c.created_at DESC");
$stmt->bind_param("i", $counselor_id);
$stmt->execute();
$result = $stmt->get_result();

// Hitung statistik
$stmt = $conn->prepare("SELECT COUNT(*) FROM consultations WHERE counselor_id = ?");
$stmt->bind_param("i", $counselor['counselor_id']);
$stmt->execute();
$stmt->bind_result($total_sessions);
$stmt->fetch();
$stmt->close();

$stats = [
    'total_sessions' => $total_sessions,
    'avg_rating' => number_format($counselor['rating'] ?? 0, 1),
    'upcoming' => count($consultations)
];

$page_title = "Dashboard Konselor";
require_once '../includes/header.php';
?>

<section class="dashboard">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h1>
            <p>Dashboard Konselor</p>
        </header>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Sesi</h3>
                <p><?= $stats['total_sessions'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Rating Rata-rata</h3>
                <p><?= $stats['avg_rating'] ?> / 5</p>
            </div>
            <div class="stat-card">
                <h3>Sesi Mendatang</h3>
                <p><?= $stats['upcoming'] ?></p>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Aksi Cepat</h2>
            <div class="quick-actions-grid">
                <a href="profile.php" class="action-card">Edit Profil</a>
                <a href="reports.php" class="action-card">Laporan Sesi</a>
                <a href="../admin/manage-articles.php" class="action-card">Kelola Artikel</a>
            </div>
        </div>

        <div class="upcoming-sessions">
            <h2>Jadwal Konsultasi Terdekat</h2>
            <?php if (count($consultations) > 0): ?>
                <?php foreach ($consultations as $consultation): ?>
                    <div class="session-card">
                        <div class="session-info">
                            <img src="<?= htmlspecialchars($consultation['profile_pic'] ?? 'default.png') ?>" class="profile-pic" alt="Profile">
                            <div>
                                <h4><?= htmlspecialchars($consultation['username']) ?></h4>
                                <p><?= date('d M Y, H:i', strtotime($consultation['schedule'])) ?></p>
                            </div>
                        </div>
                        <!-- <a href="consultation_detail.php?id=<?= $consultation['consultation_id'] ?>" class="btn-small">Detail</a> -->
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada sesi terjadwal.</p>
            <?php endif; ?>
        </div>
    </div>
    <section class="review-section">
      <h2>Ulasan dari Klien</h2>
    
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="review-box">
            <div class="review-header">
              <strong><?= htmlspecialchars($row['user_name']) ?></strong>
              <div class="rating">
                <?= str_repeat('⭐', $row['rating']) ?>
              </div>
            </div>
    
            <em><?= nl2br(htmlspecialchars($row['review'])) ?></em>
    
            <div class="divider"></div>
    
            <div class="timestamp"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Belum ada ulasan dari klien.</p>
      <?php endif; ?>
    </section>
</section>


<style>
    body {
        font-family: 'Poppins', 'Open Sans', sans-serif;
        background: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .dashboard {
        padding: 50px 20px;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header h1 {
        font-size: 2.2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .dashboard-header p {
        color: #6c757d;
        margin-bottom: 40px;
    }

    .stats {
        display: flex;
        gap: 20px;
        margin-bottom: 50px;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1 1 250px;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card h3 {
        font-size: 1.2em;
        margin-bottom: 10px;
        color: #495057;
    }

    .stat-card p {
        font-size: 2.5em;
        font-weight: 700;
        color: #007bff;
    }

    .quick-actions {
        margin-bottom: 50px;
    }

    .quick-actions h2 {
        font-size: 1.5em;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .quick-actions-grid {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .action-card {
        flex: 1 1 250px;
        background: #007bff;
        color: #ffffff;
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .action-card:hover {
        background: #0056b3;
    }

    .upcoming-sessions h2 {
        font-size: 1.5em;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .session-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .session-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-pic {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .btn-small {
        background-color: #007bff;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9em;
        transition: background-color 0.3s;
    }

    .btn-small:hover {
        background-color: #0056b3;
    }

    .review-section {
  max-width: 1200px;
  margin: 30px auto;
  padding: 20px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.review-section h2 {
  font-size: 24px;
  color: #333;
  text-align: center;
  margin-bottom: 20px;
  font-weight: 600;
}

.review-box {
  background-color: #f9fafb;
  border: 1px solid #e0e4e8;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review-box:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.review-box .review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.review-box .review-header strong {
  font-size: 16px;
  color: #00796b;
}

.review-box .review-header .rating {
  font-size: 14px;
  color: #fbc02d;
}

.review-box em {
  font-size: 14px;
  color: #555;
  line-height: 1.6;
}

.review-box .timestamp {
  font-size: 12px;
  color: #9e9e9e;
  text-align: right;
  margin-top: 10px;
}

.review-box .divider {
  margin: 20px 0;
  height: 1px;
  background: #e0e4e8;
}


    @media (max-width: 768px) {

        .stats,
        .quick-actions-grid {
            flex-direction: column;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>