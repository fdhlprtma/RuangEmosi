<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "Daftar Sebagai Konselor";
require_once 'includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  redirect('login.php', 'Silakan login terlebih dahulu.');
}

// Proses form jika metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $specialization = sanitize($_POST['specialization']);
  $qualifications = sanitize($_POST['qualifications']);
  $experience = sanitize($_POST['experience']);

  // Validasi input
  $errors = [];
  if (empty($specialization)) $errors[] = "Spesialisasi wajib diisi!";
  if (empty($experience)) $errors[] = "Pengalaman wajib diisi!";
  if ($_FILES['certificate']['error'] !== UPLOAD_ERR_OK) $errors[] = "Sertifikat wajib diupload!";

  // Upload dokumen jika tidak ada error
  $certificate_path = '';
  if (empty($errors) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'assets/certificates/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true); // Buat folder jika belum ada

    $file_ext = pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION);
    $allowed_ext = ['pdf', 'doc', 'docx'];

    if (in_array(strtolower($file_ext), $allowed_ext)) {
      $file_name = 'certificate_' . $user_id . '_' . time() . '.' . $file_ext;
      move_uploaded_file($_FILES['certificate']['tmp_name'], $upload_dir . $file_name);
      $certificate_path = $file_name;
    } else {
      $errors[] = "Hanya file PDF/DOC yang diperbolehkan!";
    }
  }

  // Simpan ke database jika tidak ada error
  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO counselor_applications 
      (user_id, specialization, qualifications, experience, certificate_path) 
      VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $specialization, $qualifications, $experience, $certificate_path);

    if ($stmt->execute()) {
      redirect('user/profile.php', 'Aplikasi konselor berhasil dikirim! Tunggu verifikasi admin.');
    } else {
      $errors[] = "Gagal menyimpan data: " . $conn->error;
    }
  }
}
?>

<!-- Form HTML -->
<div class="container mt-5">
  <h2 class="mb-4"><?= $page_title ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= $error ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="apply-counselor.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="specialization" class="form-label">Spesialisasi</label>
      <input type="text" class="form-control" id="specialization" name="specialization" required>
    </div>

    <div class="mb-3">
      <label for="qualifications" class="form-label">Kualifikasi (Pendidikan)</label>
      <textarea class="form-control" id="qualifications" name="qualifications" rows="3"></textarea>
    </div>

    <div class="mb-3">
      <label for="experience" class="form-label">Pengalaman (Tahun)</label>
      <input type="number" class="form-control" id="experience" name="experience" min="1" required>
    </div>

    <div class="mb-3">
      <label for="certificate" class="form-label">Upload Sertifikat (PDF/DOC)</label>
      <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.doc,.docx" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>

<?php require_once 'includes/footer.php'; ?>