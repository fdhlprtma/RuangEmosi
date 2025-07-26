<?php include 'includes/header.php'; ?>

<section class="contact-section">
  <div class="container">
    <div class="contact-header">
      <h2>Hubungi Kami</h2>
      <p>Jika Anda memiliki pertanyaan, saran, atau ingin berbicara lebih lanjut, kami dengan senang hati siap membantu Anda.</p>
    </div>

    <div class="contact-content">
      <!-- Form Kontak -->
      <div class="contact-form">
        <h3>Form Kontak</h3>
        <form action="submit_contact.php" method="POST">
          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" placeholder="Nama Anda" required>
          </div>
          
          <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" placeholder="Email Anda" required>
          </div>

          <div class="form-group">
            <label for="subject">Subjek</label>
            <input type="text" id="subject" name="subject" placeholder="Subjek Pesan" required>
          </div>

          <div class="form-group">
            <label for="message">Pesan</label>
            <textarea id="message" name="message" placeholder="Tulis pesan Anda di sini" rows="6" required></textarea>
          </div>

          <div class="form-group">
            <button type="submit" class="btn-submit">Kirim Pesan</button>
          </div>
        </form>
      </div>

      <!-- Informasi Kontak -->
      <div class="contact-info">
        <h3>Informasi Kontak</h3>
        <p><strong>Email:</strong> <a href="https://mail.google.com/mail/?view=cm&to=eksperimencoding@gmail.com" target="_blank">teamsupport@ruangemosi.com</a></p>
        <p><strong>Telepon:</strong> +62 123 456 789</p>
        <p><strong>Alamat:</strong> Jl. Kebun Raya No.10, Jakarta, Indonesia</p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
  .contact-section {
    padding: 60px 20px;
    background-color: #f9f9f9;
  }

  .contact-header h2 {
    font-size: 36px;
    margin-bottom: 10px;
    color: #333;
  }

  .contact-header p {
    font-size: 18px;
    color: #555;
    margin-bottom: 40px;
  }

  .contact-content {
    display: flex;
    justify-content: space-between;
    gap: 20px;
  }

  .contact-form, .contact-info {
    width: 48%;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .contact-form h3 {
    font-size: 24px;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    font-size: 16px;
    color: #333;
  }

  .form-group input, .form-group textarea {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 8px;
  }

  .form-group textarea {
    resize: vertical;
  }

  .btn-submit {
    background-color:rgb(77, 73, 213);
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn-submit:hover {
    background-color: rgb(41, 38, 133);
  }

  .contact-info h3 {
    font-size: 24px;
    margin-bottom: 20px;
  }

  .contact-info p {
    font-size: 16px;
    color: #333;
  }

  .contact-info a {
    color: rgb(77, 73, 213);
    text-decoration: none;
  }

  .contact-info a:hover {
    text-decoration: underline;
  }
</style>
