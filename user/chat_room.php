<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_user(); // <-- Cek kalau user yang login

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$consultation_id = (int)$_GET['id'];

// Cek konsultasi valid
$stmt = $conn->prepare("
    SELECT c.*, cu.username, cu.profile_pic
    FROM consultations c
    JOIN counselors cu ON c.counselor_id = cu.counselor_id
    WHERE c.consultation_id = ? AND c.user_id = ?
");
$stmt->bind_param("ii", $consultation_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$consultation = $result->fetch_assoc();

if (!$consultation) {
    echo "<h2>Konsultasi tidak ditemukan.</h2>";
    exit();
}

$page_title = "Ruang Chat";
require_once '../includes/header.php';
?>

<section class="chat-room">
    <div class="container">
        <div class="chat-header">
            <img src="<?= htmlspecialchars($consultation['profile_pic'] ?? '../assets/default-profile.png') ?>" alt="Profile Picture" class="profile-pic-small">
            <h2><?= htmlspecialchars($consultation['username']) ?></h2>
        </div>

        <div class="chat-messages" id="chat-messages">
            <!-- Pesan muncul di sini -->
        </div>

        <form id="chat-form" class="chat-form" autocomplete="off">
            <input type="hidden" id="consultation_id" value="<?= $consultation_id ?>">
            <input type="text" id="message" placeholder="Ketik pesan..." required>
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</section>

<script>
const consultationId = document.getElementById('consultation_id').value;

// Fungsi ambil pesan
function fetchMessages() {
    fetch('../includes/fetch_messages.php?id=' + consultationId)
        .then(response => response.json())
        .then(data => {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '';
            data.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', msg.sender === 'user' ? 'sent' : 'received');

                const contentDiv = document.createElement('div');
                contentDiv.classList.add('message-content');
                contentDiv.innerHTML = msg.message;

                messageDiv.appendChild(contentDiv);
                chatMessages.appendChild(messageDiv);
            });
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
}

// Fungsi kirim pesan
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message');
    const message = messageInput.value.trim();
    if (message === '') return;

    fetch('../includes/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            consultation_id: consultationId,
            message: message,
            sender: 'user'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            fetchMessages(); // Refresh
        } else {
            alert(data.message || 'Gagal mengirim pesan.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
    });
});

// Ambil pesan setiap 2 detik
setInterval(fetchMessages, 2000);

// Load pertama kali
fetchMessages();
</script>

<?php require_once '../includes/footer.php'; ?>
