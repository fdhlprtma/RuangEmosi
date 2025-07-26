<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . '/forum', 'Komentar tidak valid');
}

$reply_id = (int)$_GET['id'];
$post_id = (int)$_GET['post_id'];

// Verify ownership
$stmt = $conn->prepare("SELECT user_id FROM forum_replies WHERE reply_id = ?");
$stmt->bind_param("i", $reply_id);
$stmt->execute();
$reply = $stmt->get_result()->fetch_assoc();

if (!$reply || (!is_admin() && $_SESSION['user_id'] != $reply['user_id'])) {
    redirect(BASE_URL . "/forum/post.php?id=$post_id", 'Akses ditolak');
}

// Delete reply
$stmt = $conn->prepare("DELETE FROM forum_replies WHERE reply_id = ?");
$stmt->bind_param("i", $reply_id);
if ($stmt->execute()) {
    // Update reply count
    $conn->query("UPDATE forum_posts SET reply_count = reply_count - 1 WHERE post_id = $post_id");
    redirect(BASE_URL . "/forum/post.php?id=$post_id", 'Komentar berhasil dihapus');
}

redirect(BASE_URL . "/forum/post.php?id=$post_id", 'Gagal menghapus komentar');
?>