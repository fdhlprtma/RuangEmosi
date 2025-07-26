<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data konselor
$stmt = $conn->prepare("SELECT * FROM counselors WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$counselor = $stmt->get_result()->fetch_assoc();

// Ambil daftar user yang sudah mengirim pesan
$stmt = $conn->prepare("SELECT DISTINCT u.user_id, u.username, u.profile_pic, m.timestamp 
                        FROM messages m 
                        JOIN users u ON m.sender_id = u.user_id
                        WHERE m.receiver_id = ?
                        ORDER BY m.timestamp DESC");

$stmt->bind_param("i", $counselor['counselor_id']);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = "Dashboard Konselor";
require_once '../includes/header.php';
?>

<section class="dashboard">
    <div class="container">
        <h1>Daftar Pengguna yang Dapat Dihubungi</h1>
        <div class="user-list">
            <?php foreach ($users as $user): ?>
                <div class="user-item">
                    <img src="<?= BASE_URL ?>/assets/images/profiles/<?= htmlspecialchars($user['profile_pic'] ?? 'default-profile.jpg') ?>" 
                         alt="Profile" class="profile-pic">
                    <span><?= htmlspecialchars($user['username']) ?></span>
                    <a href="chat_konselor.php?user_id=<?= $user['user_id'] ?>" class="btn btn-primary">Chat</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
