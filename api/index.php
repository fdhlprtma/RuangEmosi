<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "Ruang Aman untuk Kesehatan Mental";
require_once 'includes/header.php';
?>
<div id="main-content">
<style>
  /* General Reset */
  #main-content body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  /* Hero Section */
  #main-content .hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #e0f2ff, #f0f9ff);
    padding: 60px 10%;
    border-radius: 12px;
    margin: 20px;
    flex-wrap: wrap;
  }

  #main-content .hero-content {
    max-width: 600px;
  }

  #main-content .hero h1 {
    font-size: 2.5rem;
    color: #1e3a8a;
    margin-bottom: 10px;
  }

  #main-content .hero p {
    font-size: 1.1rem;
    color: #4b5563;
    margin-bottom: 20px;
  }

  #main-content .hero-buttons .btn {
    margin-right: 10px;
  }

  #main-content .hero-image img {
    max-width: 500px;
    border-radius: 300px;
    box-shadow: none;
  }

  /* Buttons */
  #main-content .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
  }

  #main-content .btn-primary {
    background-color: #2563eb;
    color: white;
    border: none;
  }

  #main-content .btn-secondary {
    background-color: #f3f4f6;
    color: #111827;
    border: 1px solid #e5e7eb;
  }

  #main-content .btn:hover {
    transform: translateY(-2px);
  }

  /* Features Section */
  #main-content .features {
    padding: 60px 10%;
    background-color: #fff;
  }

  #main-content .features h2 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 1.8rem;
    color: #111827;
  }

  #main-content .feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
  }

  #main-content .feature-card {
    background-color: #f9fafb;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  }

  #main-content .feature-icon {
    font-size: 2rem;
    color: #2563eb;
    margin-bottom: 15px;
  }

  /* Testimonials */
  #main-content .testimonials {
    padding: 60px 10%;
    background-color: #e0f2fe;
    text-align: center;
  }

  #main-content .testimonials h2 {
    font-size: 1.8rem;
    color: #111827;
    margin-bottom: 30px;
  }

  #main-content .testimonial-slider {
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
  }

  #main-content .testimonial {
    max-width: 600px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  }

  #main-content .testimonial-content p {
    font-style: italic;
    color: #4b5563;
  }

  #main-content .testimonial-author {
    margin-top: 10px;
    font-weight: bold;
    color: #111827;
  }

  /* Call To Action */
  #main-content .cta2 {
    padding: 60px 10%;
    background-color: #f0f9ff;
    text-align: center;
  }

  #main-content .cta2 h2 {
    font-size: 2rem;
    color: #1e3a8a;
    margin-bottom: 20px;
  }

  #main-content .cta2 p {
    color: #4b5563;
    margin-bottom: 20px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    #main-content .hero {
      flex-direction: column;
      text-align: center;
    }

    #main-content .hero-image {
      margin-top: 20px;
    }

    #main-content nav ul {
      flex-direction: column;
      align-items: center;
    }
  }

</style>

<section class="hero">
  <div class="hero-content">
    <h1>Ruang Aman untuk Kesehatan Mentalmu</h1>
    <p>Temukan dukungan, informasi, dan komunitas yang memahami perasaanmu.</p>
    <div class="hero-buttons">
      <a href="<?php echo BASE_URL; ?>/tests/phq9.php" class="btn btn-primary">Mulai Tes Mental</a>
      <a href="<?php echo BASE_URL; ?>/forum" class="btn btn-secondary">Forum Curhat</a>
    </div>
  </div>
  <div class="hero-image">
    <img src="<?php echo BASE_URL; ?>/assets/images/pictures/—Pngtree—psychologist service 3d illustration psychotherapy_14619334.png" alt="Hero Image">
  </div>
</section>

<section class="features">
  <h2>Kenapa Memilih RuangEmosi?</h2>
  <div class="feature-grid">
    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-lock"></i></div>
      <h3>Aman & Anonim</h3>
      <p>Bebas berekspresi tanpa takut diketahui identitas aslimu.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-heart"></i></div>
      <h3>Dukungan Komunitas</h3>
      <p>Temahami dan didukung oleh orang-orang yang berpengalaman serupa.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-book"></i></div>
      <h3>Informasi Terpercaya</h3>
      <p>Artikel dan sumber informasi yang akurat tentang kesehatan mental.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-headset"></i></div>
      <h3>Konseling Terjangkau</h3>
      <p>Akses ke konselor berpengalaman dengan harga terjangkau.</p>
    </div>
  </div>
</section>

<section class="testimonials">
  <h2>Apa Kata Mereka?</h2>
  <div class="testimonial-slider">
    <div class="testimonial">
      <div class="testimonial-content">
        <p>"RuangEmosi membantuku memahami bahwa aku tidak sendirian. Forumnya sangat mendukung."</p>
      </div>
      <div class="testimonial-author"><span>- Anonim, 22 tahun</span></div>
    </div>
    <div class="testimonial">
      <div class="testimonial-content">
        <p>"Tes kesehatan mentalnya sederhana tapi hasilnya cukup akurat. Aku jadi tahu harus mulai dari mana."</p>
      </div>
      <div class="testimonial-author"><span>- R, 19 tahun</span></div>
    </div>
  </div>
</section>

<section class="cta2">
  <h2>Siap Memulai Perjalanan Kesehatan Mentalmu?</h2>
  <p>Bergabunglah dengan ribuan anak muda lainnya yang telah menemukan dukungan di RuangEmosi.</p>
  <a href="<?php echo is_logged_in() ? BASE_URL . '/user/dashboard.php' : BASE_URL . '/register.php'; ?>" class="btn btn-primary">Mulai Sekarang</a>
</section>

</div><!-- End of main-content -->

<?php require_once 'includes/footer.php'; ?> 