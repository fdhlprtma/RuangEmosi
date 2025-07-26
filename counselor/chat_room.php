<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$consultation_id = (int)$_GET['id'];

// Cek konsultasi valid
$stmt = $conn->prepare("
    SELECT c.*, u.username, u.profile_pic
    FROM consultations c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.consultation_id = ?
");
$stmt->bind_param("i", $consultation_id);
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
            <div class="loading">Memuat pesan...</div>
        </div>

        <form id="chat-form" class="chat-form" autocomplete="off">
            <input type="hidden" id="consultation_id" value="<?= $consultation_id ?>">
            <input type="text" id="message" placeholder="Ketik pesan..." required>
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</section>

<style>
.chat-room {
    padding: 30px 20px;
    background-color: #f2f5fa;
    height: 90vh;
    display: flex;
    flex-direction: column;
}

.chat-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background-color: #ffffff;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.chat-header .profile-pic-small {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.chat-messages {
    flex: 1;
    background-color: #ffffff;
    border-radius: 10px;
    padding: 15px;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    position: relative;
}

.chat-messages .loading {
    text-align: center;
    color: #888;
    margin-top: 20px;
}

.chat-form {
    display: flex;
    margin-top: 15px;
}

.chat-form input[type="text"] {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px 0 0 10px;
    background: #fff;
    font-size: 1rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.chat-form button {
    background-color: #007bff;
    border: none;
    padding: 0 20px;
    border-radius: 0 10px 10px 0;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background 0.3s;
}

.chat-form button:hover {
    background-color: #0056b3;
}

.message {
    margin-bottom: 15px;
}

.message.sent {
    text-align: right;
}

.message.received {
    text-align: left;
}

.message-content {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 15px;
    background-color: #e0e0e0;
    max-width: 70%;
    word-wrap: break-word;
}

.message.sent .message-content {
    background-color: #007bff;
    color: white;
}
</style>

<script>
// Ambil dan tampilkan semua pesan
function fetchMessages() {
    const consultationId = document.getElementById('consultation_id').value;
    fetch('../includes/fetch_messages.php?id=' + consultationId)
        .then(response => response.json())
        .then(data => {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '';

            // CEK DULU apakah data array
            if (Array.isArray(data)) {
                data.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message', msg.sender === 'counselor' ? 'sent' : 'received');

                    const contentDiv = document.createElement('div');
                    contentDiv.classList.add('message-content');
                    contentDiv.innerHTML = msg.message;

                    messageDiv.appendChild(contentDiv);
                    chatMessages.appendChild(messageDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else {
                console.error('Data bukan array:', data);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
        });
}


// Kirim pesan
// Fungsi kirim chat
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const messageInput = document.getElementById('message');
    const consultationId = document.getElementById('consultation_id').value;
    const message = messageInput.value.trim();
    if (message === '') return;

    fetch('../includes/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // <-- penting
        },
        body: JSON.stringify({
            consultation_id: consultationId,
            message: message,
            sender: 'counselor' // <-- tambah sender supaya sesuai
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            fetchMessages(); // refresh pesan
        } else {
            alert(data.message || 'Gagal mengirim pesan.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
    });
});


// Refresh otomatis chat setiap 2 detik
setInterval(fetchMessages, 2000);

// Pertama kali load
fetchMessages();
</script>

<?php require_once '../includes/footer.php'; ?>
