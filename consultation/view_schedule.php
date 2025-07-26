<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Cek apakah konselor_id ada di URL
if (!isset($_GET['counselor_id'])) {
    die("Konselor tidak ditemukan.");
}

$counselor_id = (int) $_GET['counselor_id'];

// Ambil data konselor dan pengguna terkait (username, profile_pic)
$stmt = $conn->prepare("
    SELECT c.*, u.username, u.profile_pic
    FROM counselors c 
    JOIN users u ON c.user_id = u.user_id 
    WHERE c.counselor_id = ?
");
$stmt->bind_param("i", $counselor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $counselor = $result->fetch_assoc();
} else {
    die("Konselor tidak ditemukan.");
}

// Ambil jadwal yang tersedia dari konselor
$stmt = $conn->prepare("SELECT * FROM consultations WHERE counselor_id = ? AND user_id IS NULL AND schedule >= NOW() ORDER BY schedule ASC");
$stmt->bind_param("i", $counselor_id);
$stmt->execute();
$schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = "Lihat Jadwal Konselor";
require_once '../includes/header.php';
?>

<section class="view-schedule">
    <div class="container">
        <h1>Jadwal Tersedia - <?= htmlspecialchars($counselor['username']) ?></h1>
        <p>Spesialisasi: <?= htmlspecialchars($counselor['specialization']) ?></p>
        
        <!-- Tampilkan jadwal jika tersedia -->
        <?php if (count($schedules) > 0): ?>
            <ul class="schedule-list">
                <?php foreach ($schedules as $schedule): ?>
                    <li>
                        <?= date('d M Y H:i', strtotime($schedule['schedule'])) ?> - 
                        <?= htmlspecialchars($schedule['duration']) ?> menit
                        <form action="book.php" method="POST" style="display:inline;">
                            <input type="hidden" name="consultation_id" value="<?= $schedule['consultation_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success">Pesan Jadwal</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Belum ada jadwal tersedia dari konselor ini.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
