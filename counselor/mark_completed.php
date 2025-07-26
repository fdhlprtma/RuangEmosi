<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil ID konsultasi dari form
    $consultation_id = $_POST['id'];

    // Update status konsultasi menjadi 'completed'
    $query = "UPDATE consultations SET status = 'completed' WHERE consultation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $consultation_id);
    $stmt->execute();

    // Redirect ke halaman laporan
    $_SESSION['message'] = 'Sesi telah ditandai selesai.';
    header('Location: reports.php');
} else {
    // Jika bukan request POST
    $_SESSION['error'] = 'Gagal menandai sesi selesai.';
    header('Location: reports.php');
}
?>
