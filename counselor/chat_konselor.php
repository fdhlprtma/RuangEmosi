<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['counselor_id'])) {
    header("Location: ../login.php");
    exit();
}

$counselor_id = $_SESSION['counselor_id'];
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Ambil nama user
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    echo "User tidak ditemukan!";
    exit();
}

// Ambil pesan antara konselor dan user
$stmt = $conn->prepare("
    SELECT m.*, u.username AS sender_name, u.profile_pic AS sender_pic, c.username AS counselor_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.user_id
    LEFT JOIN counselors c ON m.receiver_id = c.counselor_id
    WHERE (m.sender_id = ? AND m.receiver_id = ?)
       OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.timestamp ASC
");
$stmt->bind_param("iiii", $user_id, $counselor_id, $counselor_id, $user_id);
$stmt->execute();
$messages = $stmt->get_result();

// Kirim pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp, sender_role) VALUES (?, ?, ?, NOW(), 'counselor')");
        $stmt->bind_param("iis", $counselor_id, $user_id, $message);
        $stmt->execute();
        header("Location: counselor_chat.php?user_id=" . $user_id);
        exit();
    }
}

$page_title = "Chat dengan User";
require_once '../includes/header.php';
?>

<div class="chat-container">
    <h2>Chat dengan User <?= htmlspecialchars($user['username']) ?></h2>
    
    <div class="chat-box">
        <?php if ($messages && $messages->num_rows > 0): ?>
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message <?= $message['sender_id'] == $counselor_id ? 'sent' : 'received' ?>">
                    <p><strong><?= htmlspecialchars($message['sender_name']) ?>:</strong> <?= nl2br(htmlspecialchars($message['message'])) ?></p>
                    <span><?= date('d M Y H:i', strtotime($message['timestamp'])) ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada pesan. Kirim pesan pertama untuk memulai percakapan.</p>
        <?php endif; ?>
    </div>

    <form action="" method="POST" class="message-form">
        <textarea name="message" rows="3" placeholder="Kirim pesan..." required></textarea>
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>

<style>
.chat-container {
    padding: 20px;
    max-width: 600px;
    margin: auto;
}

.chat-box {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    height: 400px;
    overflow-y: auto;
    margin-bottom: 20px;
}

.message {
    margin-bottom: 10px;
}

.message.sent {
    text-align: right;
}

.message.received {
    text-align: left;
}

.message p {
    margin: 0;
}

.message span {
    display: block;
    font-size: 0.8rem;
    color: #888;
}

.message-form textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
}

.message-form button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.message-form button:hover {
    background: #0056b3;
}
</style>

<?php require_once '../includes/footer.php'; ?>
