<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<section class="privacy-policy">
  <div class="privacy-container">
    <h1 class="privacy-main-title">Kebijakan Privasi</h1>
    <p class="privacy-last-updated">Terakhir diperbarui: 1 Januari 2024</p>

    <div class="privacy-section">
      <h2 class="privacy-section-title">1. Pengenalan</h2>
      <p>Kami di <?php echo SITE_NAME; ?> menghargai dan melindungi privasi pengguna. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p>
    </div>

    <div class="policy-section">
      <h2 class="section-title">2. Informasi yang Kami Kumpulkan</h2>
      <ul class="policy-list">
        <li>
          <strong>Informasi Pribadi:</strong>
          <p>Nama, alamat email, informasi kontak yang Anda berikan saat registrasi</p>
        </li>
        <li>
          <strong>Data Penggunaan:</strong>
          <p>Riwayat aktivitas, preferensi, dan interaksi dengan platform</p>
        </li>
        <li>
          <strong>Data Teknis:</strong>
          <p>Alamat IP, tipe browser, informasi perangkat dan sistem operasi</p>
        </li>
      </ul>
    </div>

    <div class="policy-section">
      <h2 class="section-title">3. Penggunaan Data</h2>
      <p>Kami menggunakan data untuk:</p>
      <ol class="policy-list">
        <li>Menyediakan dan memelihara layanan</li>
        <li>Meningkatkan pengalaman pengguna</li>
        <li>Komunikasi dengan pengguna</li>
        <li>Analisis dan pengembangan produk</li>
        <li>Kepatuhan hukum</li>
      </ol>
    </div>

    <div class="policy-section">
      <h2 class="section-title">4. Berbagi Data</h2>
      <p>Kami tidak menjual atau menyewakan data pribadi Anda. Data mungkin dibagikan dengan:</p>
      <ul class="policy-list">
        <li>Penyedia layanan pihak ketiga yang mendukung operasi kami</li>
        <li>Otoritas hukum jika diwajibkan oleh hukum</li>
        <li>Mitra bisnis dalam kerangka kemitraan yang sah</li>
      </ul>
    </div>

    <div class="policy-section">
      <h2 class="section-title">5. Hak Pengguna</h2>
      <p>Anda berhak untuk:</p>
      <div class="rights-grid">
        <div class="right-item">
          <i class="fas fa-eye"></i>
          <h3>Akses Data</h3>
          <p>Meminta salinan data pribadi Anda</p>
        </div>
        <div class="right-item">
          <i class="fas fa-edit"></i>
          <h3>Perubahan Data</h3>
          <p>Memperbarui atau mengoreksi informasi</p>
        </div>
        <div class="right-item">
          <i class="fas fa-trash"></i>
          <h3>Penghapusan</h3>
          <p>Meminta penghapusan data pribadi</p>
        </div>
        <div class="right-item">
          <i class="fas fa-ban"></i>
          <h3>Penolakan Pemrosesan</h3>
          <p>Menolak pemrosesan data tertentu</p>
        </div>
      </div>
    </div>

    <div class="policy-section">
      <h2 class="section-title">6. Keamanan Data</h2>
      <p>Kami menggunakan berbagai langkah keamanan termasuk:</p>
      <ul class="security-list">
        <li>Enkripsi data selama transmisi</li>
        <li>Sistem autentikasi dua faktor</li>
        <li>Audit keamanan berkala</li>
        <li>Pembatasan akses data</li>
      </ul>
    </div>

    <div class="policy-section">
      <h2 class="section-title">7. Cookie</h2>
      <p>Kami menggunakan cookie untuk:</p>
      <div class="cookie-types">
        <div class="cookie-item">
          <h3>Cookie Esensial</h3>
          <p>Untuk operasional dasar situs</p>
        </div>
        <div class="cookie-item">
          <h3>Cookie Preferensi</h3>
          <p>Menyimpan pengaturan pengguna</p>
        </div>
        <div class="cookie-item">
          <h3>Cookie Analitik</h3>
          <p>Menganalisis pola penggunaan</p>
        </div>
      </div>
      <p>Anda dapat mengelola preferensi cookie melalui pengaturan browser.</p>
    </div>

    <div class="policy-section">
      <h2 class="section-title">8. Perubahan Kebijakan</h2>
      <p>Kami dapat memperbarui kebijakan ini secara berkala. Perubahan akan diberitahukan melalui:</p>
      <ul class="notification-list">
        <li>Pemberitahuan di platform</li>
        <li>Email ke alamat terdaftar</li>
        <li>Update tanggal revisi di halaman ini</li>
      </ul>
    </div>

    <div class="policy-section contact-section">
      <h2 class="section-title">9. Hubungi Kami</h2>
      <p>Untuk pertanyaan terkait kebijakan privasi, hubungi:</p>
      <div class="contact-info">
        <p><i class="fas fa-envelope"></i> <?php echo CONTACT_EMAIL; ?></p>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo COMPANY_ADDRESS; ?></p>
        <p><i class="fas fa-phone"></i> <?php echo CONTACT_PHONE; ?></p>
      </div>
    </div>
  </div>
</section>

<style>
.privacy-policy .privacy-container {
  max-width: 1000px;
  margin: 0 auto;
  background: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.privacy-policy .privacy-main-title {
  font-size: 2.5rem;
  color: #0f172a;
  margin-bottom: 1rem;
  text-align: center;
}

.privacy-policy .privacy-last-updated {
  text-align: center;
  color: #64748b;
  margin-bottom: 3rem;
}

.privacy-policy .privacy-section {
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.privacy-policy .privacy-section-title {
  color: #0f172a;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  padding-left: 1rem;
  border-left: 4px solid #0f172a;
}

.privacy-policy .policy-list {
  padding-left: 2rem;
  list-style-type: none;
}

.privacy-policy .policy-list li {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.privacy-policy .rights-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.privacy-policy .right-item {
  text-align: center;
  padding: 1.5rem;
  background: #f1f5f9;
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.privacy-policy .right-item:hover {
  transform: translateY(-5px);
}

.privacy-policy .right-item i {
  font-size: 2rem;
  color: #0f172a;
  margin-bottom: 1rem;
}

.privacy-policy .security-list {
  columns: 2;
  column-gap: 2rem;
}

.privacy-policy .security-list li {
  break-inside: avoid;
  margin-bottom: 1rem;
  padding: 0.5rem;
}

.privacy-policy .cookie-types {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin: 2rem 0;
}

.privacy-policy .cookie-item {
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-align: center;
}

.privacy-policy .contact-info {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
}

.privacy-policy .contact-info p {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

</style>

<?php require_once 'includes/footer.php'; ?>