<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!is_logged_in()) {
    redirect(BASE_URL . '/login.php', 'Silakan login untuk mengakses layanan konseling.');
}

$page_title = "Jadwalkan Konsultasi";
require_once '../includes/header.php';

$counselor = null;

// Ambil data konselor berdasarkan ID dari URL
if (isset($_GET['counselor_id'])) {
    $counselor_id = intval($_GET['counselor_id']);
    
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
    }
}

// Proses jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Pastikan ini mendapatkan nilai yang benar
    $consultation_id = intval($_POST['consultation_id']);

    // Validasi input
    if ($consultation_id) {
        // Update status konsultasi menjadi 'booked'
        $stmt = $conn->prepare("UPDATE consultations SET user_id = ? WHERE consultation_id = ?");
        $stmt->bind_param("ii", $user_id, $consultation_id);
        
        if ($stmt->execute()) {
            echo "<div class='success-message'>✅ Konsultasi berhasil dijadwalkan!</div>";
        } else {
            echo "<div class='error-message'>❗ Gagal menyimpan konsultasi. Silakan coba lagi.</div>";
        }
    } else {
        echo "<div class='error-message'>❗ Data tidak lengkap. Harap pilih jadwal.</div>";
    }
}
?>

<section class="booking-section">
    <div class="container">
        <h2>Jadwal Konsultasi dengan 
            <?php echo $counselor ? htmlspecialchars($counselor['username']) : 'Konselor Tidak Ditemukan'; ?>
        </h2>

        <?php if ($counselor): ?>
        <form method="POST">
            <input type="hidden" name="consultation_id" value="<?php echo $consultation_id; ?>">

            <button type="submit" class="btn btn-primary">Jadwalkan Konsultasi</button>
        </form>

        <hr>

        <h3>Informasi Konselor</h3>
        <div class="counselor-info">
            <img src="<?php echo $counselor['profile_pic'] ? BASE_URL . '/assets/images/profiles/' . $counselor['profile_pic'] : BASE_URL . '/assets/images/default-profile.jpg'; ?>" alt="Foto Konselor" width="100">
            <p><strong><?php echo htmlspecialchars($counselor['username']); ?></strong></p>
            <p>Spesialisasi: <?php echo htmlspecialchars($counselor['specialization']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($counselor['qualifications'])); ?></p>
        </div>
        <?php else: ?>
            <p class="error-message">Konselor tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
