<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

require_counselor();

header('Content-Type: application/json');

// Ambil data dari POST JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['consultation_id'], $data['message'], $data['sender'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit();
}

$consultation_id = (int) $data['consultation_id'];
$message = trim($data['message']);
$sender = $data['sender']; // "counselor" atau "user"

// Cek kalau pesan kosong
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Pesan kosong.']);
    exit();
}

// Simpan pesan ke database
$stmt = $conn->prepare("
    INSERT INTO messages (consultation_id, sender, message, created_at)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iss", $consultation_id, $sender, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal kirim pesan.']);
}
?>
