<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Pastikan user sudah login
if (!is_logged_in()) {
    redirect(BASE_URL . '/login.php', 'Silakan login terlebih dahulu');
}

// Validasi parameter ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . '/forum', 'Post tidak valid');
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Mulai transaksi database
$conn->begin_transaction();

try {
    // Dapatkan data post dan verifikasi kepemilikan
    $stmt = $conn->prepare("SELECT user_id, category FROM forum_posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();

    // Validasi post dan hak akses
    if (!$post) {
        throw new Exception('Post tidak ditemukan');
    }
    
    if (!is_admin() && $post['user_id'] != $user_id) {
        throw new Exception('Anda tidak memiliki akses untuk menghapus post ini');
    }

    // Hapus semua komentar terkait post
    $delete_replies = $conn->prepare("DELETE FROM forum_replies WHERE post_id = ?");
    $delete_replies->bind_param("i", $post_id);
    $delete_replies->execute();

    // Hapus post itu sendiri
    $delete_post = $conn->prepare("DELETE FROM forum_posts WHERE post_id = ?");
    $delete_post->bind_param("i", $post_id);
    $delete_post->execute();

    // Commit transaksi jika semua query berhasil
    $conn->commit();

    // Redirect dengan pesan sukses
    redirect(BASE_URL . '/forum', 'Post berhasil dihapus beserta semua komentarnya');

} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $conn->rollback();
    
    // Log error untuk debugging
    error_log('Delete Post Error: ' . $e->getMessage());
    
    // Redirect dengan pesan error
    redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/forum', 'Gagal menghapus post: ' . $e->getMessage());
}

// Tutup koneksi
$conn->close();
?>