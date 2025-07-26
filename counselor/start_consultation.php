<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$consultation_id = (int)$_GET['id'];

// Ambil data konsultasi
$stmt = $conn->prepare("
    SELECT c.*, u.username, u.profile_pic
    FROM consultations c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.consultation_id = ?
");
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();
$consultation = $result->fetch_assoc();

if (!$consultation) {
    echo "<h2>Konsultasi tidak ditemukan.</h2>";
    exit();
}

$page_title = "Mulai Konsultasi";
require_once '../includes/header.php';
?>

<section class="start-consultation">
    <div class="container">
        <div class="consultation-header">
            <h1>Mulai Konsultasi</h1>
            <p>Berikut detail sesi konsultasi:</p>
        </div>

        <div class="consultation-details">
            <div class="user-profile">
                <img src="<?= htmlspecialchars($consultation['profile_pic'] ?? '../assets/default-profile.png') ?>" alt="Profile Picture" class="profile-pic">
                <h3><?= htmlspecialchars($consultation['username']) ?></h3>
            </div>

            <ul class="consultation-info">
                <li><strong>Topik:</strong> <?= htmlspecialchars($consultation['topic'] ?? '(Tidak ada topik)') ?></li>
                <li><strong>Catatan Tambahan:</strong> <?= nl2br(htmlspecialchars($consultation['notes'] ?? '(Tidak ada catatan)')) ?></li>
                <li><strong>Jadwal:</strong> <?= htmlspecialchars(date('d M Y, H:i', strtotime($consultation['schedule']))) ?></li>
                <li><strong>Durasi:</strong> <?= htmlspecialchars($consultation['duration']) ?> menit</li>
            </ul>

            <div class="start-session">
                <a href="chat_room.php?id=<?= $consultation['consultation_id'] ?>" class="btn btn-primary">
                    <i class="fas fa-comments"></i> Mulai Sesi Chat
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.start-consultation {
    padding: 40px 20px;
    background-color: #f7faff;
}

.consultation-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.consultation-header p {
    font-size: 1.1rem;
    color: #666;
}

.consultation-details {
    background: white;
    padding: 30px;
    margin-top: 30px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
}

.user-profile {
    text-align: center;
    margin-bottom: 20px;
}

.user-profile .profile-pic {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}

.user-profile h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #222;
}

.consultation-info {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    font-size: 1.1rem;
    color: #444;
}

.consultation-info li {
    margin-bottom: 10px;
}

.start-session {
    text-align: center;
    margin-top: 30px;
}

.start-session .btn-primary {
    background-color: #007bff;
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 1.2rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.3s;
}

.start-session .btn-primary:hover {
    background-color: #0056b3;
}
</style>

<?php require_once '../includes/footer.php'; ?>
