<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'auth.php';

require_counselor();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit();
}

$consultation_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT sender, message, created_at
    FROM messages
    WHERE consultation_id = ?
    ORDER BY created_at ASC
");
$stmt->bind_param("i", $consultation_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
