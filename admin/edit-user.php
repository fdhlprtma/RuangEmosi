<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

if (!isset($_GET['id'])) {
    redirect('manage-users.php', 'ID pengguna tidak ditemukan.');
}

$user_id = (int) $_GET['id'];

// Ambil data pengguna
$stmt = $conn->prepare("SELECT user_id, username, email, is_admin FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    redirect('manage-users.php', 'Pengguna tidak ditemukan.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Update data pengguna
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE user_id = ?");
    $stmt->bind_param('ssii', $username, $email, $is_admin, $user_id);

    if ($stmt->execute()) {
        redirect('manage-users.php', 'Pengguna berhasil diperbarui.');
    } else {
        $errors[] = 'Gagal memperbarui pengguna.';
    }
}

$page_title = "Edit Pengguna";
require_once '../includes/header.php';
?>

<section class="admin-users">
    <div class="container">
        <h1>Edit Pengguna</h1>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" name="is_admin" id="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>>
                    <label for="is_admin">Akses Admin</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
