<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$counselor_id = isset($_GET['counselor_id']) ? (int)$_GET['counselor_id'] : null;

// Ambil nama konselor jika ada counselor_id
if ($counselor_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $counselor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $counselor = $result->fetch_assoc();
} else {
    echo "Konselor tidak ditemukan!";
    exit();
}

// Ambil pesan antara user dan konselor
$stmt = $conn->prepare("
    SELECT m.*, u.username AS sender_name, u.profile_pic AS sender_pic, c.username AS counselor_name
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.user_id
    LEFT JOIN users c ON m.receiver_id = c.user_id
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
        echo "Pesan yang dikirim: " . htmlspecialchars($message); // Debugging: Tampilkan pesan
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
        if ($stmt === false) {
            die('Error preparing statement: ' . $conn->error); // Debugging: Error preparing statement
        }
        $stmt->bind_param("iis", $user_id, $counselor_id, $message);
        $result = $stmt->execute();
        if ($result) {
            header("Location: chat_user.php?counselor_id=" . $counselor_id); // Refresh halaman setelah mengirim pesan
            exit();
        } else {
            die('Error executing query: ' . $stmt->error); // Debugging: Error executing query
        }
    }
}

$page_title = "Chat dengan Konselor";
require_once '../includes/header.php';
?>

<div class="chat-container">
    <h2>Chat dengan Konselor <?= htmlspecialchars($counselor['username']) ?></h2>
    
    <div class="chat-box">
        <?php if ($messages && $messages->num_rows > 0): ?>
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message <?= $message['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                    <p>
                        <strong><?= htmlspecialchars($message['sender_name']) ?>:</strong>
                        <?= nl2br(htmlspecialchars($message['message'])) ?>
                    </p>
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

<!-- CSS di bawah ini digabungkan langsung di halaman -->
<style>
.chat-container {
    padding: 20px;
    max-width: 600px;
    margin: auto;
    background: #f1f1f1;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.chat-box {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    height: 400px;
    overflow-y: auto;
    margin-bottom: 20px;
    box-shadow: inset 0 1px 10px rgba(0, 0, 0, 0.1);
}

.message {
    margin-bottom: 15px;
}

.message.sent {
    text-align: right;
    background-color: #d4f7e2;
    padding: 10px;
    border-radius: 8px;
}

.message.received {
    text-align: left;
    background-color: #f7f7f7;
    padding: 10px;
    border-radius: 8px;
}

.message p {
    margin: 0;
    font-size: 1rem;
}

.message span {
    display: block;
    font-size: 0.8rem;
    color: #888;
    margin-top: 5px;
}

.message-form textarea {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    font-size: 1rem;
    resize: vertical;
}

.message-form button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 1rem;
}

.message-form button:hover {
    background: #0056b3;
}
</style>

<?php require_once '../includes/footer.php'; ?>
