<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

// Pastikan ada ID konsultasi di URL
if (!isset($_GET['id'])) {
  header('Location: dashboard.php');
  exit();
}

$consultation_id = (int) $_GET['id'];

// Ambil detail konsultasi
$stmt = $conn->prepare("
    SELECT c.*, u.username, u.email, u.profile_pic
    FROM consultations c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.consultation_id = ?
");
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$consultation = $result->fetch_assoc();

if (!$consultation) {
  echo "Data konsultasi tidak ditemukan.";
  exit();
}

$page_title = "Detail Konsultasi";
require_once '../includes/header.php';
?>

<section class="detail-page">
  <div class="detail-container">
    <a href="dashboard.php" class="back-button">&larr; Kembali ke Dashboard</a>

    <div class="detail-card">
      <div class="profile-section">
        <img src="<?= htmlspecialchars($consultation['profile_pic'] ?? 'default.png') ?>" class="profile-pic-large" alt="Profile">
        <div class="profile-info">
          <h2><?= htmlspecialchars($consultation['username']) ?></h2>
          <p><?= htmlspecialchars($consultation['email']) ?></p>
        </div>
      </div>

      <div class="consultation-info">
        <h3>Informasi Konsultasi</h3>
        <ul>
          <li><strong>Tanggal:</strong> <?= date('d M Y', strtotime($consultation['schedule'])) ?></li>
          <li><strong>Waktu:</strong> <?= date('H:i', strtotime($consultation['schedule'])) ?></li>
          <li><strong>Topik:</strong> <?= htmlspecialchars($consultation['topic'] ?? '(Belum diisi)') ?></li>
          <li><strong>Catatan Tambahan:</strong> <?= nl2br(htmlspecialchars($consultation['notes'] ?? '(Belum diisi)')) ?></li>
        </ul>
      </div>

      <div class="consultation-actions">
        <a href="start_consultation.php?id=<?= $consultation['consultation_id'] ?>" class="btn-start">Mulai Konsultasi</a>
      </div>
    </div>
  </div>
</section>

<style>
  .detail-page {
    padding: 50px 20px;
    background: #f8f9fa;
  }

  .detail-container {
    max-width: 800px;
    margin: 0 auto;
  }

  .back-button {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #007bff;
  }

  .detail-card {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .profile-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
  }

  .profile-pic-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #007bff;
  }

  .profile-info h2 {
    margin: 0;
    font-size: 1.8em;
    color: #2c3e50;
  }

  .profile-info p {
    margin: 5px 0 0;
    color: #6c757d;
  }

  .consultation-info h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
  }

  .consultation-info ul {
    list-style: none;
    padding: 0;
  }

  .consultation-info li {
    margin-bottom: 10px;
    color: #495057;
  }

  .consultation-actions {
    text-align: center;
    margin-top: 30px;
  }

  .btn-start {
    background-color: #28a745;
    color: #ffffff;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 8px;
    font-size: 1em;
    transition: background-color 0.3s;
  }

  .btn-start:hover {
    background-color: #218838;
  }
</style>

<?php require_once '../includes/footer.php'; ?>