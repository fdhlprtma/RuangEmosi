<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

// Hapus pengguna
if (isset($_GET['delete_user_id'])) {
    $user_id_to_delete = (int) $_GET['delete_user_id'];

    if ($user_id_to_delete != $_SESSION['user_id']) {
        try {
            $conn->begin_transaction();

            // 1. Hapus aktivitas forum
            // Hapus balasan forum
            $stmt = $conn->prepare("DELETE FROM forum_replies WHERE user_id = ?");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            // Hapus post forum dan balasannya (jika ada ON DELETE CASCADE)
            $stmt = $conn->prepare("DELETE FROM forum_posts WHERE user_id = ?");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            // 2. Hapus data konseling
            // Hapus konsultasi
            $stmt = $conn->prepare("DELETE FROM consultations WHERE counselor_id IN (SELECT counselor_id FROM counselors WHERE user_id = ?)");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            // 3. Hapus jadwal konselor
            $stmt = $conn->prepare("DELETE FROM counselor_availability WHERE counselor_id IN (SELECT counselor_id FROM counselors WHERE user_id = ?)");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            // 4. Hapus data konselor
            $stmt = $conn->prepare("DELETE FROM counselors WHERE user_id = ?");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            // 5. Hapus pengguna
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param('i', $user_id_to_delete);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            redirect('manage-users.php', 'Pengguna berhasil dihapus.');
            
        } catch (Exception $e) {
            $conn->rollback();
            redirect('manage-users.php', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    } else {
        redirect('manage-users.php', 'Tidak dapat menghapus pengguna yang sedang login.');
    }
}

// Ambil daftar pengguna
$stmt = $conn->prepare("SELECT user_id, username, email, is_admin FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = "Kelola Pengguna";
require_once '../includes/header.php';
?>

<section class="admin-users">
    <div class="container">
        <h1>Kelola Pengguna</h1>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-<?= isset($_GET['success']) ? 'success' : 'danger' ?>">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Akses Admin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['is_admin'] ? 'Ya' : 'Tidak' ?></td>
                        <td>
                            <a href="edit-user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?delete_user_id=<?= $user['user_id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>