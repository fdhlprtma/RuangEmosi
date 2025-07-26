<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_login();

if (isset($_GET['id'])) {
  $post_id = $_GET['id'];

  // Cek apakah post ada dan milik pengguna yang sedang login
  $stmt = $conn->prepare("SELECT * FROM forum_posts WHERE post_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Hapus post
    $stmt = $conn->prepare("DELETE FROM forum_posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    // Redirect setelah berhasil dihapus
    header("Location: dashboard.php?msg=post_hapus_berhasil");
    exit;
  } else {
    // Redirect jika post tidak ditemukan atau tidak milik pengguna
    header("Location: dashboard.php?msg=post_tidak_ditemukan");
    exit;
  }
} else {
  // Redirect jika ID post tidak ada
  header("Location: dashboard.php?msg=post_tidak_ditemukan");
  exit;
}
