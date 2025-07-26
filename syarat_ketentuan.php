<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<section class="terms-and-conditions">
  <div class="privacy-container">
    <h1 class="privacy-main-title">Syarat dan Ketentuan</h1>
    <p class="privacy-last-updated">Terakhir diperbarui: 1 Januari 2024</p>

    <div class="privacy-section">
      <h2 class="privacy-section-title">1. Pengenalan</h2>
      <p>Selamat datang di <?php echo SITE_NAME; ?>. Dengan mengakses dan menggunakan layanan kami, Anda setuju untuk terikat dengan syarat dan ketentuan yang tercantum dalam halaman ini.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">2. Penggunaan Layanan</h2>
      <p>Anda berhak untuk menggunakan layanan yang kami tawarkan sesuai dengan ketentuan yang berlaku. Kami berhak untuk mengubah atau menghentikan layanan tanpa pemberitahuan terlebih dahulu.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">3. Akun Pengguna</h2>
      <p>Untuk menggunakan sebagian besar fitur, Anda harus membuat akun dengan informasi yang akurat dan up-to-date. Anda bertanggung jawab penuh atas keamanan akun Anda.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">4. Tanggung Jawab Pengguna</h2>
      <p>Pengguna wajib memastikan bahwa mereka tidak melanggar hukum yang berlaku atau merugikan pihak lain saat menggunakan layanan kami.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">5. Pembayaran dan Pengembalian Dana</h2>
      <p>Untuk layanan berbayar, Anda setuju untuk membayar sesuai dengan harga yang berlaku. Pengembalian dana hanya dapat dilakukan sesuai dengan kebijakan kami.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">6. Pembatasan Tanggung Jawab</h2>
      <p>Kami tidak bertanggung jawab atas kerugian langsung atau tidak langsung yang terjadi akibat penggunaan layanan kami.</p>
    </div>

    <div class="privacy-section">
      <h2 class="privacy-section-title">7. Perubahan Syarat dan Ketentuan</h2>
      <p>Kami berhak untuk mengubah syarat dan ketentuan ini kapan saja. Perubahan akan diberitahukan melalui pemberitahuan di platform atau melalui email yang terdaftar.</p>
    </div>

    <div class="privacy-section contact-section">
      <h2 class="privacy-section-title">8. Hubungi Kami</h2>
      <p>Jika Anda memiliki pertanyaan atau masalah terkait syarat dan ketentuan ini, Anda dapat menghubungi kami melalui:</p>
      <div class="contact-info">
        <p><i class="fas fa-envelope"></i> <?php echo CONTACT_EMAIL; ?></p>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo COMPANY_ADDRESS; ?></p>
        <p><i class="fas fa-phone"></i> <?php echo CONTACT_PHONE; ?></p>
      </div>
    </div>
  </div>
</section>

<style>
.terms-and-conditions {
  padding: 4rem 2rem;
  background: #f8fafc;
  min-height: 100vh;
}

.privacy-container {
  max-width: 1000px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.privacy-main-title {
  font-size: 2.5rem;
  color: #0f172a;
  margin-bottom: 1rem;
  text-align: center;
}

.privacy-last-updated {
  text-align: center;
  color: #64748b;
  margin-bottom: 3rem;
}

.privacy-section {
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.privacy-section-title {
  color: #0f172a;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  padding-left: 1rem;
  border-left: 4px solid #0f172a;
}

.privacy-list {
  padding-left: 2rem;
  list-style-type: none;
}

.privacy-list li {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.rights-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.right-item {
  text-align: center;
  padding: 1.5rem;
  background: #f1f5f9;
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.right-item:hover {
  transform: translateY(-5px);
}

.right-item i {
  font-size: 2rem;
  color: #0f172a;
  margin-bottom: 1rem;
}

.security-list {
  columns: 2;
  column-gap: 2rem;
}

.security-list li {
  break-inside: avoid;
  margin-bottom: 1rem;
  padding: 0.5rem;
}

.cookie-types {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin: 2rem 0;
}

.cookie-item {
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-align: center;
}

.contact-info {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
}

.contact-info p {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

@media (max-width: 768px) {
  .privacy-container {
    padding: 2rem;
  }

  .cookie-types {
    grid-template-columns: 1fr;
  }

  .security-list {
    columns: 1;
  }

  .rights-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<?php require_once 'includes/footer.php'; ?>
