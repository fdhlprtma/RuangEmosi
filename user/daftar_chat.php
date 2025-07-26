<?php
include '../includes/config.php';
include '../includes/functions.php';
include '../includes/auth.php';
// session_start();

if (!isset($_SESSION['username'])) {
    echo "Session username tidak ditemukan. Pastikan user login dulu.";
    exit;
}

$username = $_SESSION['username'];

$query = "
    SELECT DISTINCT consultation_id 
    FROM messages
    WHERE sender = '$username'
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h2>Daftar Chat</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        $consultationId = $row['consultation_id'];
        echo "<li><a href='chat_room.php?consultation_id=$consultationId'>Masuk Chat Konsultasi #$consultationId</a></li>";
    }
    echo "</ul>";
} else {
    echo "Belum ada chat ditemukan.";
}
?>
