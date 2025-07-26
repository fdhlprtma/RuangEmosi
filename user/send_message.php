<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_user(); // Jika ini untuk user, pastikan autentikasi user
echo "receiver_id: $counselor_id"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sender_id = $_SESSION['user_id'] ?? null;
$receiver_id = $_POST['receiver_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if ($sender_id && $receiver_id && $message !== '') {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        header("Location: chat_user.php?counselor_id=$receiver_id");
        exit();
    } else {
        echo "Gagal menyimpan pesan: " . $stmt->error;
    }
} else {
    echo "Input tidak lengkap.";
}
