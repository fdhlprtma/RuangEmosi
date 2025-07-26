<?php
include '../config/db.php';
include '../includes/header.php';

$query = $conn->query("SELECT c.*, u.name FROM counselors c JOIN users u ON c.user_id = u.id");

echo "<h2>Temukan Konselor</h2>";
while ($row = $query->fetch_assoc()) {
    echo "<div style='border: 1px solid #ccc; padding: 15px; margin-bottom: 10px'>";
    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
    echo "<p><strong>Spesialisasi:</strong> " . $row['specialization'] . "</p>";
    echo "<p><strong>Pengalaman:</strong> " . $row['experience'] . "</p>";
    echo "<p><strong>Tarif:</strong> Rp" . number_format($row['hourly_rate'], 2) . " /jam</p>";
    echo "<p><strong>Rating:</strong> " . $row['rating'] . " ⭐ (" . $row['session_count'] . " sesi)</p>";
    echo "<a href='../consultation/book.php?counselor_id=" . $row['counselor_id'] . "'>Jadwalkan Konseling</a>";
    echo "</div>";
}
?>
