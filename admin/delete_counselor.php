<?php
include '../includes/config.php';
include '../includes/auth.php';

// Cek apakah admin
if (!is_admin()) {
    redirect(BASE_URL . '/admin/dashboard.php', 'Akses ditolak');
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Hapus counselor dari tabel counselors
    $deleteCounselor = $conn->prepare("DELETE FROM counselors WHERE user_id = ?");
    $deleteCounselor->bind_param("i", $user_id);

    if ($deleteCounselor->execute()) {
        // Optional: Jika ingin hapus juga dari tabel users
        // $deleteUser = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        // $deleteUser->bind_param("i", $user_id);
        // $deleteUser->execute();

        redirect(BASE_URL . '/admin/manage-counselor.php', 'Konselor berhasil dihapus');
    } else {
        redirect(BASE_URL . '/admin/manage-counselor.php', 'Gagal menghapus konselor');
    }

    $deleteCounselor->close();
} else {
    redirect(BASE_URL . '/admin/manage-counselor.php', 'ID tidak valid');
}
?>
