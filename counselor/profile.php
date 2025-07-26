<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

$counselor_id = $_SESSION['counselor_id'] ?? null;

if (!$counselor_id) {
    echo "<div class='alert alert-danger'>Counselor ID tidak ditemukan di session. Pastikan Anda login sebagai konselor.</div>";
    exit;
}

// Upload foto profil dan sertifikat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $specialization = $_POST['specialization'];
    $qualifications = $_POST['qualifications'];
    $experience = $_POST['experience'];
    $availability = $_POST['availability'];
    $hourly_rate = $_POST['hourly_rate'];

    $cert_path = $photo_path = null;

    // Sertifikat
if (!empty($data['certificate_path']) && file_exists('../' . $data['certificate_path'])): ?>
  <div style="margin-top: 10px;">
      <label>Sertifikat Saat Ini:</label><br>
      <a href="../<?= htmlspecialchars($data['certificate_path']) ?>" target="_blank" class="btn btn-success">Lihat Sertifikat</a>
  </div>
<?php endif; ?>

<?php
// Foto profil
    if ($_FILES['photo']['error'] == 0) {
        $photo_path = 'assets/images/profiles/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], '../' . $photo_path);
    }

    $sql = "UPDATE counselors SET specialization=?, qualifications=?, experience=?, availability=?, hourly_rate=?";
    if ($cert_path) $sql .= ", certificate_path=?";
    if ($photo_path) $sql .= ", photo=?";
    $sql .= " WHERE counselor_id=?";

    $stmt = $conn->prepare($sql);
    if ($cert_path && $photo_path) {
        $stmt->bind_param("sssssssi", $specialization, $qualifications, $experience, $availability, $hourly_rate, $cert_path, $photo_path, $counselor_id);
    } elseif ($cert_path) {
        $stmt->bind_param("ssssssi", $specialization, $qualifications, $experience, $availability, $hourly_rate, $cert_path, $counselor_id);
    } elseif ($photo_path) {
        $stmt->bind_param("ssssssi", $specialization, $qualifications, $experience, $availability, $hourly_rate, $photo_path, $counselor_id);
    } else {
        $stmt->bind_param("sssssi", $specialization, $qualifications, $experience, $availability, $hourly_rate, $counselor_id);
    }

    $stmt->execute();
}

$data = $conn->query("SELECT * FROM counselors WHERE counselor_id = $counselor_id")->fetch_assoc();
?>

<style>
    .profile-wrapper {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        display: flex;
        gap: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        font-family: 'Segoe UI', sans-serif;
    }
    .profile-photo {
        width: 250px;
        text-align: center;
    }
    .profile-photo img {
        width: 100%;
        max-height: 250px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .form-area {
        flex: 1;
    }
    .form-area h2 {
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
        color: #34495e;
    }
    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    textarea {
        min-height: 80px;
    }
    .btn-submit {
        margin-top: 20px;
        width: 100%;
        background-color:rgb(0, 149, 255);
        color: #fff;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-submit:hover {
        background-color:rgb(5, 39, 98);
    }
    .cert-preview {
        margin-top: 10px;
    }
    .cert-preview img {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="profile-wrapper">
    <div class="profile-photo">
        <h3>Foto Profil</h3>
        <img src="../<?= $data['photo'] ?? 'assets/photos/default.jpg' ?>" alt="Foto Konselor">
        <div class="form-group" style="margin-top: 10px;">
            <label>Ganti Foto</label>
            <input type="file" name="photo" form="profileForm">
        </div>
    </div>

    <div class="form-area">
        <h2>Profil Konselor</h2>
        <form method="post" enctype="multipart/form-data" id="profileForm">
            <div class="form-group">
                <label>Spesialisasi</label>
                <input type="text" name="specialization" value="<?= htmlspecialchars($data['specialization']) ?>">
            </div>

            <div class="form-group">
                <label>Kualifikasi</label>
                <textarea name="qualifications"><?= htmlspecialchars($data['qualifications']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Pengalaman</label>
                <textarea name="experience"><?= htmlspecialchars($data['experience']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Ketersediaan</label>
                <textarea name="availability"><?= htmlspecialchars($data['availability']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Tarif per jam (Rp)</label>
                <input type="number" step="0.01" name="hourly_rate" value="<?= htmlspecialchars($data['hourly_rate']) ?>">
            </div>

            <div class="form-group">
                <label>Sertifikat Baru</label>
                <input type="file" name="certificate">
            </div>

            <?php if (!empty($data['certificate_path']) && file_exists('../' . $data['certificate_path'])): ?>
                <div class="cert-preview">
                    <label>Sertifikat Saat Ini:</label>
                    <img src="../<?= $data['certificate_path'] ?>" alt="Sertifikat">
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
