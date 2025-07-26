<?php
session_start();
require_once '/includes/config.php'; // File konfigurasi database

// Cek user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$counselor_id = 1; // ID konselor, bisa disesuaikan dengan sistem pairing

// Handle pengiriman pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) 
                           VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $counselor_id, $message);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Konselor</title>
    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #eee;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
        }
        .user-message {
            background-color: #e3f2fd;
            margin-left: auto;
        }
        .counselor-message {
            background-color: #f5f5f5;
        }
        .message-input {
            display: flex;
            gap: 10px;
        }
        textarea {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            <?php
            $stmt = $conn->prepare("SELECT * FROM chat_messages 
                                  WHERE (sender_id = ? AND receiver_id = ?)
                                  OR (sender_id = ? AND receiver_id = ?)
                                  ORDER BY timestamp ASC");
            $stmt->bind_param("iiii", $user_id, $counselor_id, $counselor_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $messageClass = ($row['sender_id'] == $user_id) ? 'user-message' : 'counselor-message';
                echo "<div class='message $messageClass'>";
                echo "<p>" . htmlspecialchars($row['message']) . "</p>";
                echo "<small>" . date('H:i', strtotime($row['timestamp'])) . "</small>";
                echo "</div>";
            }
            ?>
        </div>
        
        <form class="message-input" method="POST">
            <textarea name="message" placeholder="Ketik pesan Anda..." required></textarea>
            <button type="submit">Kirim</button>
        </form>
    </div>

    <script>
        // Auto refresh pesan setiap 2 detik
        setInterval(function() {
            fetch('get_messages.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('chatMessages').innerHTML = data;
                    // Scroll ke pesan terbaru
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }, 2000);
    </script>
</body>
</html>