<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Memastikan data sudah lengkap dan form sudah di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Mengambil data input dari form
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $subject = $_POST['subject'] ?? '';  // Menambahkan subjek
  $message = $_POST['message'] ?? '';

  // Validasi input
  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $_SESSION['error'] = "Semua kolom harus diisi.";
    header('Location: contact.php');
    exit;
  }

  // Mengirimkan email saran
  $mail = new PHPMailer(true);
  try {
    // Pengaturan SMTP Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'eksperimencoding@gmail.com';  // Ganti dengan email Anda
    $mail->Password = 'ztcj vhqw kiap clma';  // Ganti dengan app password Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Pengaturan pengirim dan penerima
    $mail->setFrom($email, $name);  // Pengirim berasal dari email pengirim
    $mail->addAddress('eksperimencoding@gmail.com', 'RuangEmosi');  // Ganti dengan alamat email tujuan saran

    // Konten email
    $mail->isHTML(true);
    $mail->Subject = $subject;  // Menggunakan subjek yang diinputkan oleh pengguna

    // Membuat badan email HTML dengan gaya (CSS)
    $mail->Body = "
      <html>
        <head>
          <style>
            body {
              font-family: 'Arial', sans-serif;
              background-color: #f9f9f9;
              padding: 20px;
              color: #333;
            }
            h2 {
              color:rgb(237, 237, 237);
            }
            .email-container {
              background-color: #ffffff;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
              max-width: 600px;
              margin: auto;
            }
            .email-header {
              background-color:rgb(64, 92, 202);
              color: white;
              padding: 15px;
              border-radius: 5px;
              text-align: center;
              font-size: 24px;
            }
            .email-content {
              margin-top: 20px;
            }
            .message-box {
              background-color: #f1f1f1;
              padding: 15px;
              border: 1px solid #ddd;
              border-radius: 5px;
              font-size: 16px;
              line-height: 1.6;
              margin-top: 15px;
            }
            .email-footer {
              font-size: 14px;
              color: #777;
              text-align: center;
              margin-top: 30px;
              padding-top: 10px;
              border-top: 1px solid #ddd;
            }
            .email-footer a {
              color: #4CAF50;
              text-decoration: none;
            }
            .email-footer a:hover {
              text-decoration: underline;
            }
            .icon {
              font-size: 40px;
              color: #4CAF50;
              margin-right: 10px;
            }
          </style>
        </head>
        <body>
          <div class='email-container'>
            <div class='email-header'>
              <h2>📩 Saran Baru dari Pengguna</h2>
            </div>
            <div class='email-content'>
              <p><strong>Nama:</strong> {$name}</p>
              <p><strong>Email:</strong> {$email}</p>
              <div class='message-box'>
                <p><strong>Pesan Saran:</strong><br>{$message}</p>
              </div>
            </div>
            <div class='email-footer'>
              <p>Terima kasih atas masukan yang Anda berikan. Tim RuangEmosi akan segera menindaklanjuti.</p>
              <p>Jika Anda ingin menghubungi kami lebih lanjut, silakan kunjungi <a href='https://www.ruangemosi.com'>RuangEmosi</a></p>
            </div>
          </div>
        </body>
      </html>
    ";

    // Kirim email
    $mail->send();
    $_SESSION['message'] = "Saran berhasil dikirim. Terima kasih atas masukan Anda!";
  } catch (Exception $e) {
    $_SESSION['error'] = "Pesan gagal dikirim: {$mail->ErrorInfo}";
  }

  // Redirect ke halaman kontak setelah pengiriman
  header('Location: contact.php');
  exit;
}
?>