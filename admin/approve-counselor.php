<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

// Proses persetujuan
if (isset($_GET['approve'])) {
    $application_id = (int)$_GET['approve'];
    
    // Ambil data aplikasi
    $application = $conn->query("
        SELECT * FROM counselor_applications 
        WHERE id = $application_id
    ")->fetch_assoc();
    
    // Update user jadi konselor
    $conn->query("
        UPDATE users 
        SET is_counselor = 1 
        WHERE user_id = {$application['user_id']}
    ");
    
    // Tambahkan ke tabel konselor
    $conn->query("
        INSERT INTO counselors (user_id, specialization, qualifications, experience) 
        VALUES (
            {$application['user_id']},
            '{$application['specialization']}',
            '{$application['qualifications']}',
            '{$application['experience']}'
        )
    ");
    
    // Hapus aplikasi
    $conn->query("DELETE FROM counselor_applications WHERE id = $application_id");
    
    redirect('approve-counselor.php', 'Konselor berhasil disetujui');
}

// Proses penolakan
if (isset($_GET['reject'])) {
    $application_id = (int)$_GET['reject'];
    
    // Hapus aplikasi yang ditolak
    $conn->query("DELETE FROM counselor_applications WHERE id = $application_id");
    
    redirect('approve-counselor.php', 'Aplikasi konselor berhasil ditolak');
}

// Ambil daftar aplikasi
$applications = $conn->query("
    SELECT a.*, u.username, u.email 
    FROM counselor_applications a
    JOIN users u ON a.user_id = u.user_id
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Persetujuan Konselor";
require_once '../includes/header.php';
?>

<section class="approve-counselors">
    <div class="container">
        <h1>Daftar Aplikasi Konselor</h1>
        
        <div class="applications-list">
            <?php foreach ($applications as $app): ?>
                <div class="application-card">
                    <div class="applicant-info">
                        <h3><?= $app['username'] ?></h3>
                        <p><?= $app['email'] ?></p>
                    </div>
                    
                    <div class="application-details">
                        <p><strong>Spesialisasi:</strong> <?= $app['specialization'] ?></p>
                        <p><strong>Kualifikasi:</strong> <?= $app['qualifications'] ?></p>
                        <p><strong>Pengalaman:</strong> <?= $app['experience'] ?></p>
                        <a href="<?= BASE_URL ?>/assets/certificates/<?= $app['certificate_path'] ?>" 
                           target="_blank" class="btn btn-small">
                           <i class="fas fa-file-pdf"></i> Lihat Sertifikat
                        </a>
                    </div>
                    
                    <div class="application-actions">
                        <!-- Tombol Setujui -->
                        <a href="?approve=<?= $app['id'] ?>" class="btn btn-success">
                            <i class="fas fa-check"></i> Setujui
                        </a>
                        
                        <!-- Tombol Tolak -->
                        <a href="?reject=<?= $app['id'] ?>" class="btn btn-danger">
                            <i class="fas fa-times"></i> Tolak
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.approve-counselors {
    padding: 40px 0;
}

.applications-list {
    margin-top: 30px;
}

.application-card {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    gap: 20px;
    padding: 20px;
    margin-bottom: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.applicant-info h3 {
    margin-bottom: 5px;
}

.application-details p {
    margin: 5px 0;
}

.application-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

.application-actions .btn {
    margin: 0 10px;
}

.application-actions .btn-success {
    background-color: #28a745;
    color: white;
}

.application-actions .btn-danger {
    background-color: #dc3545;
    color: white;
}
</style>

<?php require_once '../includes/footer.php'; ?>
