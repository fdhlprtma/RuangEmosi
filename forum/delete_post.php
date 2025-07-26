<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!is_admin()) {
    redirect(BASE_URL . '/forum/index.php', 'Akses ditolak');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);

    // Hapus post
    $stmt = $conn->prepare("DELETE FROM forum_posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        redirect(BASE_URL . '/forum/index.php', 'Post berhasil dihapus.');
    } else {
        redirect(BASE_URL . '/forum/index.php', 'Gagal menghapus post.');
    }

    $stmt->close();
} else {
    redirect(BASE_URL . '/forum/index.php');
}
