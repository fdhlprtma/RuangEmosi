<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!is_logged_in()) {
    redirect(BASE_URL . '/login.php', 'Silakan login untuk mengakses layanan konseling.');
}

// Ambil data dari form
$counselor_id = $_POST['counselor_id'] ?? null;
$schedule = $_POST['schedule'] ?? null;
$duration = $_POST['duration'] ?? 60;  // Durasi default 60 menit

if (!$counselor_id || !$schedule) {
    echo "<p>Data tidak lengkap. Silakan coba lagi.</p>";
    exit;
}

// Masukkan data ke dalam tabel konsultasi
$user_id = get_logged_in_user_id();
if (!$user_id) {
    echo "<p>Anda harus login untuk menjadwalkan konsultasi.</p>";
    exit;
}

$query = "INSERT INTO consultations (user_id, counselor_id, schedule, duration, status) 
          VALUES (?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisi", $user_id, $counselor_id, $schedule, $duration);

if ($stmt->execute()) {
    // Set session flash message untuk pemberitahuan
    $_SESSION['flash_message'] = "Jadwal konsultasi telah berhasil dijadwalkan. Anda akan segera dihubungi oleh konselor.";
} else {
    // Set session flash message untuk error
    $_SESSION['flash_message'] = "Gagal menjadwalkan konsultasi. Silakan coba lagi.";
}

// Redirect kembali ke halaman schedule dengan pesan flash
header('Location: schedule.php?counselor_id=' . $counselor_id);
exit();
?>
