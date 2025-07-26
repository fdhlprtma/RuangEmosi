<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$user = get_user_data($user_id);

$page_title = "Profil Pengguna";
require_once '../includes/header.php';

// Ambil data konsultasi mendatang
$upcoming_consultations = [];
$stmt = $conn->prepare("SELECT * FROM consultations WHERE user_id = ? AND schedule >= NOW() ORDER BY schedule ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcoming_consultations[] = $row;
}

// Ambil data hasil tes mental
$test_results = [];
$result = $conn->query("SELECT * FROM test_results WHERE user_id = $user_id");
while ($row = $result->fetch_assoc()) {
    $test_results[] = $row;
}

// Ambil data postingan forum terbaru
$recent_posts = [];
$result = $conn->query("SELECT * FROM forum_posts WHERE user_id = $user_id");
while ($row = $result->fetch_assoc()) {
    $recent_posts[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $birth_date = sanitize($_POST['birth_date']);
    $gender = sanitize($_POST['gender']);
    $bio = sanitize($_POST['bio']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    // Handle profile picture upload
    if ($_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/profiles/';
        $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_ext), $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
                $stmt->bind_param("si", $filename, $user_id);
                $stmt->execute();
            }
        }
    }

    // Update data user
    $stmt = $conn->prepare("UPDATE users SET 
                          full_name = ?, 
                          birth_date = ?, 
                          gender = ?, 
                          bio = ?, 
                          is_anonymous = ? 
                          WHERE user_id = ?");
    $stmt->bind_param("sssssi", $full_name, $birth_date, $gender, $bio, $is_anonymous, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['is_anonymous'] = $is_anonymous;
        redirect(BASE_URL . '/user/profile.php', 'Profil berhasil diperbarui!');
    } else {
        $error = "Gagal memperbarui profil. Silakan coba lagi.";
    }
}
?>

<section class="profile">
    <div class="container">
        <div class="profile-header">
            <h1>Profil Saya</h1>
            <a href="<?php echo BASE_URL; ?>/user/dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-picture">
                    <img src="<?php echo $user['profile_pic'] ? 
                              BASE_URL . '/assets/images/profiles/' . $user['profile_pic'] : 
                              BASE_URL . '/assets/images/default-profile.jpg'; ?>" 
                         alt="Profile Picture">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
                          method="POST" 
                          enctype="multipart/form-data"
                          class="upload-form">
                        <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        <label for="profile_pic" class="btn btn-small">
                            <i class="fas fa-camera"></i> Ganti Foto
                        </label>
                    </form>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <i class="fas fa-calendar-check"></i>
                        <span><?php echo count($upcoming_consultations); ?> Konsultasi</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-file-medical"></i>
                        <span><?php echo count($test_results); ?> Tes Mental</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-comments"></i>
                        <span><?php echo count($recent_posts); ?> Post Forum</span>
                    </div>
                </div>
            </div>

            <div class="profile-form">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_date">Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date" 
                                   value="<?php echo htmlspecialchars($user['birth_date']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Jenis Kelamin</label>
                            <select id="gender" name="gender">
                                <option value="L" <?php echo $user['gender'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?php echo $user['gender'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                <option value="Lainnya" <?php echo $user['gender'] === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
    <label for="bio">Bio</label>
    <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
</div>


                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_anonymous" name="is_anonymous" 
                            <?php echo $user['is_anonymous'] ? 'checked' : ''; ?>>
                        <label for="is_anonymous">Mode Anonim (username tidak akan ditampilkan di forum)</label>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
