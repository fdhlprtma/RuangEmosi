<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if ($_POST['action'] == 'accepted') {
  $consultation_id = $_POST['id'];
  $stmt = $conn->prepare("UPDATE consultations SET status = 'confirmed' WHERE consultation_id = ?");
  $stmt->bind_param("i", $consultation_id);
  $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $consultation_id = $_POST['id'] ?? null;
  $action = $_POST['action'] ?? '';
  $meeting_link = $_POST['meeting_link'] ?? null;
  $number_phone = $_POST['number_phone'] ?? null;
  $notes = $_POST['notes'] ?? null;

  if (!$consultation_id || !in_array($action, ['accepted', 'rejected'])) {
    $_SESSION['error'] = "Permintaan tidak valid.";
    header('Location: reports.php');
    exit;
  }


  // Ambil data konsultasi dan user
  $stmt = $conn->prepare("SELECT c.*, u.email, u.username, cu.user_id AS counselor_user_id, cu.counselor_id, us.username AS counselor_name
        FROM consultations c
        JOIN users u ON c.user_id = u.user_id
        JOIN counselors cu ON c.counselor_id = cu.counselor_id
        JOIN users us ON cu.user_id = us.user_id
        WHERE c.consultation_id = ?");
  $stmt->bind_param("i", $consultation_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $consultation = $result->fetch_assoc();
  $stmt->close();

  if (!$consultation) {
    $_SESSION['error'] = "Konsultasi tidak ditemukan.";
    header('Location: reports.php');
    exit;
  }

  $status_update = ($action === 'accepted') ? 'accepted' : 'rejected';

  if ($status_update === 'accepted') {
    $stmt = $conn->prepare("UPDATE consultations SET status = ?, notes = ?, meeting_link = ? WHERE consultation_id = ?");
    $stmt->bind_param("sssi", $status_update, $notes, $meeting_link, $consultation_id);
  } else {
    $stmt = $conn->prepare("UPDATE consultations SET status = ? WHERE consultation_id = ?");
    $stmt->bind_param("si", $status_update, $consultation_id);
  }

  if ($stmt->execute()) {
    // Kirim email ke user
    $mail = new PHPMailer(true);
    try {
      // Pengaturan SMTP Gmail
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'eksperimencoding@gmail.com';       // Ganti dengan email Anda
      $mail->Password = 'ztcj vhqw kiap clma';          // Ganti dengan app password Gmail
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Pengirim dan penerima
      $mail->setFrom('your-email@gmail.com', 'RuangEmosi');
      $mail->addAddress($consultation['email'], $consultation['username']);
      $mail->isHTML(true);
      $mail->Subject = ($status_update === 'accepted') ? 'Sesi Konsultasi Diterima' : 'Sesi Konsultasi Ditolak';

      if ($status_update === 'accepted') {
        $mail->Body = "Hai {$consultation['username']},<br><br>" .
          "Sesi konsultasimu telah <strong>DITERIMA</strong> oleh <strong>{$consultation['counselor_name']}</strong>.<br><br>" .
          "🗓 Tanggal: <strong>{$consultation['schedule']}</strong><br>" .
          "⏰ Waktu: <strong>{$consultation['start_time']} - {$consultation['end_time']}</strong><br>" .
          "🔗 Link Pertemuan: <a href='{$meeting_link}' target='_blank'>{$meeting_link}</a><br>" .
          "📱Nomor WhatsApp: <a href='https://wa.me/{$number_phone}' target='_blank'>{$number_phone}</a><br><br>" .
          "📌 Catatan Konselor:<br>{$notes}<br><br>" .
          "Salam sehat,<br>Tim RuangEmosi";
      } else {
        $mail->Body = "Hai {$consultation['username']},<br><br>" .
          "Maaf, sesi konsultasimu <strong>DITOLAK</strong> oleh konselor.<br>" .
          "Silakan jadwalkan ulang atau hubungi konselor lain.<br><br>" .
          "Salam sehat,<br>Tim RuangEmosi";
      }

      $mail->send();
      $_SESSION['message'] = "Status berhasil diperbarui dan email notifikasi dikirim.";
    } catch (Exception $e) {
      $_SESSION['error'] = "Status diperbarui, tapi gagal mengirim email: {$mail->ErrorInfo}";
    }
  } else {
    $_SESSION['error'] = "Gagal mengupdate status: " . $stmt->error;
  }

  $stmt->close();
}

header('Location: reports.php');
exit;
