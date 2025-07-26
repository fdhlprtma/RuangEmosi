<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil input dari form
    $counselor_id = $_POST['counselor_id'];
    $schedule = $_POST['schedule'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Durasi (asumsi sudah dalam menit, misalnya 60 menit)
    $duration = 60;

    // Pastikan status adalah salah satu dari ENUM yang valid
    $status = 'pending';  // Menetapkan status sebagai 'pending'

    // Query untuk memasukkan data ke tabel consultations
    $query = "INSERT INTO consultations (user_id, counselor_id, schedule, start_time, end_time, duration, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";  // Tambahkan ? untuk status

    // Memastikan semua field sudah ada
    if (!$schedule || !$start_time || !$end_time) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: ../user/dashboard.php");
        exit;
    }

    // Persiapkan statement dan eksekusi query
    $stmt = $conn->prepare($query);
    if ($stmt) {
        // Bind parameter ke query, gunakan 's' untuk string dan 'i' untuk integer
        $stmt->bind_param("iisssis", $user_id, $counselor_id, $schedule, $start_time, $end_time, $duration, $status);

        // Eksekusi query dan cek hasilnya
        $success = $stmt->execute();

        if ($success) {
            $_SESSION['success'] = "Jadwal konsultasi telah berhasil dijadwalkan. Anda akan segera dihubungi oleh konselor.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan jadwal: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Kesalahan pada query: " . $conn->error;
    }

    // Redirect ke halaman yang sesuai setelah proses selesai
    header("Location: index.php");
    exit;
}
?>
