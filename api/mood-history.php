<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Cek parameter
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

if ($user_id <= 0) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

// Validasi bulan dan tahun
if (!checkdate($month, 1, $year)) {
    echo json_encode(['error' => 'Invalid month or year']);
    exit;
}

// Ambil data mood untuk bulan dan tahun tertentu
$start_date = "$year-$month-01";
$end_date = date('Y-m-t', strtotime($start_date));

$stmt = $conn->prepare("SELECT date, mood, notes FROM mood_tracker 
                       WHERE user_id = ? AND date BETWEEN ? AND ?
                       ORDER BY date DESC");
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$mood_history = [];
while ($row = $result->fetch_assoc()) {
    $mood_history[] = $row;
}

echo json_encode($mood_history);
?>