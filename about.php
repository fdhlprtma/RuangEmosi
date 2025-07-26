<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Data Developer
$developer_name = "M. Fadhil Pratama";
$developer_title = "Web Developer & Software Engineer";
$developer_bio = "Saya adalah developer utama platform ini. Berpengalaman dalam pengembangan web, AI, dan teknologi digital. Dengan semangat inovasi, saya berkomitmen untuk menciptakan produk yang bermanfaat dan berdampak positif.";
$developer_photo = BASE_URL . "/assets/images/profiles/fadhil1.jpg"; // Foto profil kamu

// Data Developer 2
$developer2_name = "Emil Kusmayadi";
$developer2_title = "Web Developer & Web Designer";
$developer2_bio = "Saya adalah developer kedua platform ini. Berpengalaman dalam pengembangan web, desain UI/UX, dan teknologi digital. Dengan semangat inovasi, saya berkomitmen untuk menciptakan produk yang bermanfaat dan berdampak positif.";
$developer2_photo = BASE_URL . "/assets/images/profiles/emil (1).jpg"; // Foto profil developer baru
?>


<section class="about-hero">
  <div class="about-content-wrapper">
    <div class="about-text">
      <h1 class="about-heading">Tentang Saya</h1>
      <h2 class="about-name"><?php echo htmlspecialchars($developer_name); ?></h2>
      <h3 class="about-title"><?php echo htmlspecialchars($developer_title); ?></h3>
      <p class="about-description"><?php echo nl2br(htmlspecialchars($developer_bio)); ?></p>
    </div>
    <div class="about-photo">
      <img src="<?php echo $developer_photo; ?>" alt="Foto <?php echo htmlspecialchars($developer_name); ?>" class="photo-img">
    </div>
  </div>
</section>

<section class="about-hero">
  <div class="about-content-wrapper">
    <div class="about-text">
      <h1 class="about-heading">Tentang Saya</h1>
      <h2 class="about-name"><?php echo htmlspecialchars($developer2_name); ?></h2>
      <h3 class="about-title"><?php echo htmlspecialchars($developer2_title); ?></h3>
      <p class="about-description"><?php echo nl2br(htmlspecialchars($developer2_bio)); ?></p>
    </div>
    <div class="about-photo">
      <img src="<?php echo $developer2_photo; ?>" alt="Foto <?php echo htmlspecialchars($developer2_name); ?>" class="photo-img">
    </div>
  </div>
</section>

<section class="about-us">
  <div class="container">
    <h2 class="section-title">Tentang Kami</h2>
    <p>
      <strong>RuangEmosi</strong> lahir dari semangat besar untuk mendukung kesehatan mental melalui inovasi teknologi yang canggih. Dengan misi untuk menyediakan layanan yang mudah diakses oleh siapa saja, kami telah mengembangkan platform ini dengan memadukan teknologi terkini. Sekitar <strong>80%</strong> fitur yang ada di platform ini dibangun dengan mengombinasikan kekuatan <em>AI ChatGPT</em> dan <em>DeepSeek</em>, yang memungkinkan kami untuk memberikan pengalaman yang lebih personal dan mendalam bagi pengguna. Meskipun demikian, karena kami masih dalam tahap pengembangan, Anda mungkin akan menemukan beberapa bug, error, atau respons yang belum sempurna. Kami juga menjalankan platform ini pada server dengan biaya rendah, sehingga terkadang platform mungkin terasa sedikit lambat. Kami berharap Anda dapat memahami hal ini sebagai bagian dari perjalanan kami dalam belajar dan berkembang untuk memberikan layanan yang lebih baik di masa depan.
    </p>
    <p>
      Platform ini awalnya dikembangkan sebagai bagian dari proyek lomba teknologi, namun sayangnya kami tidak dapat mendaftar karena kuota peserta yang sudah penuh. Meskipun demikian, dari kekecewaan tersebut, kami menemukan kesempatan untuk membuka RuangEmosi bagi publik secara lebih luas, agar manfaat dari platform ini dapat dirasakan oleh banyak orang yang membutuhkan dukungan untuk kesehatan mental mereka. Kami ingin mengucapkan terima kasih yang sebesar-besarnya atas kesabaran dan dukungan yang telah Anda berikan. Setiap masukan yang Anda berikan sangat berarti bagi kami dalam upaya kami untuk terus memperbaiki dan mengembangkan platform ini.
    </p>
    <p>
      Oleh karena itu, kami sangat menghargai setiap bentuk donasi yang Anda berikan, yang akan sangat membantu kami untuk membeli server dengan kualitas yang lebih baik dan meningkatkan sistem kami yang saat ini masih dalam tahap perbaikan. Dengan dukungan Anda, kami berharap bisa menciptakan pengalaman yang lebih baik dan dapat diandalkan untuk semua pengguna.
    </p>
  </div>
</section>


<section class="vision-mission">
  <div class="container">
    <h2 class="section-title">Visi & Misi</h2>
    <div class="vision">
      <h3>Visi</h3>
      <p>Menjadi platform digital terdepan yang memberikan dukungan kesehatan mental secara inovatif, dapat diakses oleh semua orang, dan memberikan dampak positif yang berkelanjutan bagi masyarakat di seluruh dunia.</p>
    </div>
    <div class="mission">
      <h3>Misi</h3>
      <ul>
        <li>Mendorong pertumbuhan komunitas yang saling mendukung dan berbagi pengetahuan tentang pentingnya kesehatan mental.</li>
        <li>Menjaga kualitas dan integritas dalam setiap fitur dan layanan yang kami tawarkan, dengan fokus pada perbaikan berkelanjutan dan pengalaman pengguna yang lebih baik.</li>
        <li>Memberikan kesempatan bagi individu untuk memperoleh akses mudah ke layanan konseling, tes mental, dan sumber daya lainnya yang dapat mendukung perjalanan mereka menuju kesehatan mental yang lebih baik.</li>
      </ul>
    </div>
  </div>
</section>


<section class="gallery">
  <div class="container">
    <h2 class="section-title">Galeri Kami</h2>
    <div class="gallery-grid">
      <div class="gallery-item">
        <img src="<?php echo BASE_URL; ?>/assets/images/gallery/team.jpg" alt="Tim Kami">
        <p>Tim Hebat</p>
      </div>
      <div class="gallery-item">
        <img src="<?php echo BASE_URL; ?>/assets/images/gallery/event.jpg" alt="Acara Khusus">
        <p>Event Spesial</p>
      </div>
      <div class="gallery-item">
        <img src="<?php echo BASE_URL; ?>/assets/images/gallery/award.jpg" alt="Penghargaan">
        <p>Penghargaan</p>
      </div>
    </div>
  </div>
</section>


<section class="timeline">
  <div class="container">
    <h2 class="section-title">Perjalanan Kami</h2>
    <div class="timeline-wrapper">
      <div class="timeline-item">
        <div class="timeline-date">2023</div>
        <div class="timeline-content">
          <h4>Ide Awal</h4>
          <p>Konsep platform ini mulai dirancang untuk menjawab kebutuhan digital masa kini.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-date">2024</div>
        <div class="timeline-content">
          <h4>Pengembangan Intensif</h4>
          <p>Fase pengembangan dan uji coba awal dilakukan, fokus pada kualitas dan user experience.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-date">2025</div>
        <div class="timeline-content">
          <h4>Peluncuran Resmi</h4>
          <p>Platform resmi diluncurkan dan siap membantu lebih banyak pengguna di seluruh dunia.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="quote">
  <div class="container">
    <blockquote>
      "Teknologi bukan hanya tentang inovasi, tetapi tentang memberdayakan kehidupan manusia menjadi lebih baik."
    </blockquote>
    <p class="author">- <?php echo htmlspecialchars($developer_name); ?></p>
  </div>
</section>

<section class="counters">
  <div class="container">
    <div class="counter-grid">
      <div class="counter-item">
        <h3><span class="counter" data-target="5000">0</span>+</h3>
        <p>Pengguna Terdaftar</p>
      </div>
      <div class="counter-item">
        <h3><span class="counter" data-target="1200">0</span>+</h3>
        <p>Sesi Konsultasi</p>
      </div>
      <div class="counter-item">
        <h3><span class="counter" data-target="15">0</span></h3>
        <p>Penghargaan & Pengakuan</p>
      </div>
    </div>
  </div>
</section>



<script>
  // Counter Animation
  const counters = document.querySelectorAll('.counter');
  counters.forEach(counter => {
    counter.innerText = '0';

    const updateCounter = () => {
      const target = +counter.getAttribute('data-target');
      const count = +counter.innerText;

      const increment = target / 200;

      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(updateCounter, 10);
      } else {
        counter.innerText = target;
      }
    };

    updateCounter();
  });
</script>


<?php require_once 'includes/footer.php'; ?>

<style>
  /* Reset */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', sans-serif;
    color: #475569;
  }

  /* Hero Section */
  .about-hero {
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
  }

  .about-content-wrapper {
    display: flex;
    align-items: center;
    max-width: 1200px;
    width: 100%;
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
  }

  .about-content-wrapper:hover {
    transform: translateY(-5px);
  }

  .about-text {
    flex: 1;
    padding: 3rem;
  }

  .about-heading {
    font-size: 2.5rem;
    color: #0f172a;
    margin-bottom: 1rem;
    font-weight: 800;
  }

  .about-name {
    font-size: 2rem;
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .about-title {
    font-size: 1.2rem;
    color: #64748b;
    margin-bottom: 1.5rem;
  }

  .about-description {
    font-size: 1rem;
    color: #475569;
    line-height: 1.8;
  }

  .about-photo {
    flex: 1;
    background: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .about-us {
  padding: 5rem 2rem;
  background: #ffffff; /* atau sesuai desain */
  text-align: center;
}


  /* Vision & Mission Section */
  .vision-mission {
    padding: 2rem 2rem;
    background: #f8fafc;
    text-align: center;
  }

  .section-title {
    font-size: 2rem;
    color: #0f172a;
    margin-bottom: 3rem;
    font-weight: 700;
  }

  .vision,
  .mission {
    max-width: 800px;
    margin: 0 auto 2rem;
    text-align: left;
  }

  .vision h3,
  .mission h3 {
    font-size: 1.5rem;
    color: #1e293b;
    margin-bottom: 1rem;
  }

  .vision p,
  .mission ul {
    font-size: 1rem;
    line-height: 1.8;
  }

  .mission ul {
    list-style: disc inside;
  }

  /* Timeline Section */
  .timeline {
    padding: 5rem 2rem;
    background: #ffffff;
  }

  .timeline-wrapper {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
  }

  .timeline-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 4px;
    height: 100%;
    background: #0f172a;
    transform: translateX(-50%);
  }

  .timeline-item {
    position: relative;
    margin-bottom: 3rem;
  }

  .timeline-item:nth-child(odd) .timeline-content {
    margin-left: 60px;
    text-align: left;
  }

  .timeline-item:nth-child(even) .timeline-content {
    margin-right: 60px;
    text-align: right;
  }

  .timeline-date {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 0;
    background: #0f172a;
    color: #ffffff;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
  }

  .timeline-content {
    background: #f1f5f9;
    padding: 1rem 2rem;
    border-radius: 10px;
    display: inline-block;
    max-width: 300px;
  }

  .timeline-content h4 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #1e293b;
  }

  /* Counter Section */
  .counters {
    padding: 5rem 2rem;
    background: #f1f5f9;
    text-align: center;
  }

  .counter-grid {
    display: flex;
    justify-content: center;
    gap: 4rem;
  }

  .counter-item h3 {
    font-size: 2.5rem;
    color: #0f172a;
  }

  .counter-item p {
    margin-top: 0.5rem;
    font-size: 1.2rem;
    color: #64748b;
  }

  /* Quote Section */
  .quote {
    padding: 4rem 2rem;
    background: #0f172a;
    color: #ffffff;
    text-align: center;
  }

  .quote blockquote {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1rem;
    font-style: italic;
  }

  .quote .author {
    font-size: 1.2rem;
    opacity: 0.8;
  }

  /* Gallery Section */
  .gallery {
    padding: 5rem 2rem;
    background: #f8fafc;
    text-align: center;
  }

  .gallery-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    margin-top: 2rem;
  }

  .gallery-item {
    width: 300px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    background: #ffffff;
    transition: transform 0.3s ease;
  }

  .gallery-item:hover {
    transform: translateY(-8px);
  }

  .gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .gallery-item p {
    padding: 1rem;
    font-size: 1.1rem;
    color: #334155;
    font-weight: 600;
  }


  /* Responsive */
  @media (max-width: 900px) {
    .about-content-wrapper {
      flex-direction: column-reverse;
    }

    .about-text,
    .about-photo {
      flex: 1 1 100%;
      padding: 2rem;
    }

    .timeline-wrapper::before {
      left: 20px;
    }

    .timeline-item:nth-child(odd) .timeline-content,
    .timeline-item:nth-child(even) .timeline-content {
      margin: 0 0 0 60px;
      text-align: left;
    }
  }
</style>