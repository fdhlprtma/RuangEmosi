<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . '/forum', 'Post tidak ditemukan');
}

$post_id = (int)$_GET['id'];

// Ambil data post
$stmt = $conn->prepare("SELECT p.*, u.username, u.profile_pic 
                       FROM forum_posts p 
                       LEFT JOIN users u ON p.user_id = u.user_id
                       WHERE p.post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect(BASE_URL . '/forum', 'Post tidak ditemukan');
}

$post = $result->fetch_assoc();

// Update view count
$conn->query("UPDATE forum_posts SET view_count = view_count + 1 WHERE post_id = $post_id");

// Proses komentar baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $content = sanitize($_POST['content']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];
    
    // Voice Note
    $voice_path = null;
    if (!empty($_POST['voice_note_path'])) {
        $voice_dir = "assets/voices/";
        $voice_name = time() . "_" . uniqid() . ".webm";
        $voice_file = $_SERVER['DOCUMENT_ROOT'] . "/RuangEmosi/" . $voice_dir . $voice_name;
        
        // Validasi MIME Type dan ukuran file
        $audio_data = base64_decode($_POST['voice_note_path']);
        if (strpos($audio_data, 'webm') === false) {
            $_SESSION['error'] = "Format audio tidak valid";
            redirect(BASE_URL . "/forum/post.php?id=$post_id#replies");
        }
        
        if (strlen($audio_data) > 2 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran audio terlalu besar (maks 2MB)";
            redirect(BASE_URL . "/forum/post.php?id=$post_id#replies");
        }
        
        file_put_contents($voice_file, $audio_data);
        $voice_path = $voice_dir . $voice_name;
    }

    // Validasi
    if (empty($content) && empty($voice_path)) {
        $_SESSION['error'] = "Harap isi teks atau rekam voice note";
        redirect(BASE_URL . "/forum/post.php?id=$post_id#replies");
        exit;
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO forum_replies (post_id, user_id, content, is_anonymous, voice_path) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $post_id, $user_id, $content, $is_anonymous, $voice_path);

    if ($stmt->execute()) {
        $conn->query("UPDATE forum_posts SET reply_count = reply_count + 1 WHERE post_id = $post_id");
        redirect(BASE_URL . "/forum/post.php?id=$post_id#replies");
    } else {
        $_SESSION['error'] = "Gagal mengirim komentar";
        redirect(BASE_URL . "/forum/post.php?id=$post_id#replies");
    }
}

// Ambil komentar
$stmt = $conn->prepare("SELECT r.*, u.username, u.profile_pic 
                       FROM forum_replies r
                       LEFT JOIN users u ON r.user_id = u.user_id
                       WHERE r.post_id = ?
                       ORDER BY r.created_at ASC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$replies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = $post['title'];
require_once '../includes/header.php';
?>

<section class="mindspace-post-container">
    <div class="emotion-container">
        <div class="post-navigation">
            <a href="<?php echo BASE_URL; ?>/forum" class="emotion-btn emotion-btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Forum
            </a>
        </div>

        <article class="mindspace-post-card">
            <div class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>

                <div class="post-meta">
                    <div class="author-info">
                        <?php if ($post['is_anonymous'] && (!is_logged_in() || $post['user_id'] != $_SESSION['user_id'])): ?>
                            <div class="anonymous-badge">
                                <i class="fas fa-user-secret"></i> Anonim
                            </div>
                        <?php else: ?>
                            <img src="<?php echo $post['profile_pic'] ?
                                            BASE_URL . '/assets/images/profiles/' . $post['profile_pic'] :
                                            BASE_URL . '/assets/images/profiles/default-pic.jpg'; ?>"
                                alt="Profile" class="profile-pic">
                            <div class="author-details">
                                <span class="username"><?php echo htmlspecialchars($post['username']); ?></span>
                                <span class="post-date"><?php echo time_ago($post['created_at']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-stats">
                        <span class="stat-item"><i class="far fa-eye"></i> <?php echo $post['view_count']; ?></span>
                        <span class="stat-item"><i class="far fa-comment"></i> <?php echo $post['reply_count']; ?></span>
                    </div>
                </div>
            </div>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                <?php if (!empty($post['voice_path'])): ?>
                    <audio controls class="voice-note-player">
                        <source src="<?= BASE_URL . '/' . htmlspecialchars($post['voice_path']) ?>" type="audio/webm">
                        Browser Anda tidak mendukung elemen audio.
                    </audio>
                <?php endif; ?>
            </div>

            <div class="post-footer">
                <div class="post-category">
                    <span class="category-badge"><?php
                                                    $categories = [
                                                        'curhat' => 'Curhat',
                                                        'tanya' => 'Tanya',
                                                        'dukungan' => 'Dukungan',
                                                        'cerita' => 'Cerita'
                                                    ];
                                                    echo $categories[$post['category']] ?? 'Umum';
                                                    ?></span>
                </div>

                <?php if (is_logged_in() && ($_SESSION['user_id'] == $post['user_id'] || is_admin())): ?>
                    <div class="post-actions">
                        <a href="<?php echo BASE_URL; ?>/forum/edit.php?id=<?php echo $post_id; ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?php echo BASE_URL; ?>/forum/delete.php?id=<?php echo $post_id; ?>"
                            class="action-btn delete-btn"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus post ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <div class="mindspace-replies-section" id="replies">
            <h2 class="replies-title"><i class="fas fa-comments"></i> Diskusi <span class="reply-count">(<?php echo count($replies); ?>)</span></h2>

            <?php if (is_logged_in()): ?>
                <div class="reply-form-container">
                    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" class="reply-form">
                        <div class="form-group">
                            <textarea name="content" rows="4" placeholder="Bagikan pemikiran atau pengalaman Anda..."></textarea>
                        </div>

                        <div class="form-group voice-recorder-container">
                            <label class="voice-recorder-label">Rekam Voice Note:</label>
                            <div class="voice-controls">
                                <button type="button" id="comment-record-btn" class="btn btn-primary">
                                    <i class="fas fa-microphone"></i> Mulai Rekam
                                </button>
                                <button type="button" id="comment-stop-btn" class="btn btn-danger" disabled>
                                    <i class="fas fa-stop"></i> Stop
                                </button>
                            </div>
                            <audio id="comment-audio" controls class="audio-preview"></audio>
                            <input type="hidden" name="voice_note_path" id="comment-voice-path">
                            <small class="audio-info">Maksimal durasi: 2 menit</small>
                        </div>

                        <div class="form-footer">
                            <?php if ($_SESSION['is_anonymous']): ?>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="is_anonymous" name="is_anonymous" checked>
                                    <label for="is_anonymous">Kirim sebagai anonim</label>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="emotion-btn emotion-btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-prompt">
                    <p>Anda perlu login untuk berpartisipasi dalam diskusi</p>
                    <a href="<?php echo BASE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="emotion-btn emotion-btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login Sekarang
                    </a>
                </div>
            <?php endif; ?>

            <div class="replies-list">
                <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="reply-card">
                            <div class="reply-header">
                                <?php if ($reply['is_anonymous'] && (!is_logged_in() || $reply['user_id'] != $_SESSION['user_id'])): ?>
                                    <div class="anonymous-badge">
                                        <i class="fas fa-user-secret"></i> Anonim
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo $reply['profile_pic'] ?
                                                    BASE_URL . '/assets/images/profiles/' . $reply['profile_pic'] :
                                                    BASE_URL . '/assets/images/profiles/default-pic.jpg'; ?>"
                                        alt="Profile" class="profile-pic">
                                    <div class="reply-author">
                                        <span class="username"><?php echo htmlspecialchars($reply['username']); ?></span>
                                        <span class="reply-date"><?php echo time_ago($reply['created_at']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="reply-content">
                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                <?php if (!empty($reply['voice_path'])): ?>
                                    <div class="voice-note-reply">
                                        <audio controls>
                                            <source src="<?= BASE_URL . '/' . htmlspecialchars($reply['voice_path']) ?>" type="audio/webm">
                                            Browser tidak mendukung pemutar audio
                                        </audio>
                                        <small class="audio-duration">Durasi tidak tersedia</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (is_logged_in() && ($_SESSION['user_id'] == $reply['user_id'] || is_admin())): ?>
                                <div class="reply-actions">
                                    <a href="#" class="action-btn edit-btn" data-id="<?php echo $reply['reply_id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/forum/delete_reply.php?id=<?php echo $reply['reply_id']; ?>&post_id=<?php echo $post_id; ?>"
                                        class="action-btn delete-btn"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="far fa-comment-dots"></i>
                        </div>
                        <h3>Belum ada diskusi</h3>
                        <p>Jadilah yang pertama berbagi pemikiran Anda</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    // Voice Recording untuk Komentar
    let commentRecorder;
    let commentAudioChunks = [];
    let maxRecordingTime = 120000; // 2 menit
    let timeoutId;

    document.getElementById('comment-record-btn').addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    sampleRate: 44100,
                    channelCount: 1,
                    echoCancellation: true
                }
            });
            
            commentRecorder = new MediaRecorder(stream, {
                mimeType: 'audio/webm;codecs=opus'
            });

            commentRecorder.ondataavailable = (e) => {
                commentAudioChunks.push(e.data);
            };

            commentRecorder.onstop = async () => {
                const audioBlob = new Blob(commentAudioChunks, { type: 'audio/webm' });
                
                // Hitung durasi audio
                const audioContext = new AudioContext();
                const arrayBuffer = await audioBlob.arrayBuffer();
                const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
                const duration = Math.round(audioBuffer.duration);
                document.querySelectorAll('.audio-duration').forEach(el => {
                    el.textContent = `Durasi: ${duration} detik`;
                });
                
                // Konversi ke Base64
                const reader = new FileReader();
                reader.onloadend = () => {
                    document.getElementById('comment-voice-path').value = 
                        reader.result.split(',')[1];
                };
                reader.readAsDataURL(audioBlob);
                
                // Preview audio
                const audioURL = URL.createObjectURL(audioBlob);
                const audioPreview = document.getElementById('comment-audio');
                audioPreview.src = audioURL;
                audioPreview.style.display = 'block';
            };

            commentRecorder.start();
            document.getElementById('comment-record-btn').disabled = true;
            document.getElementById('comment-stop-btn').disabled = false;
            commentAudioChunks = [];
            
            // Auto stop setelah 2 menit
            timeoutId = setTimeout(() => {
                if (commentRecorder.state === 'recording') {
                    commentRecorder.stop();
                    document.getElementById('comment-record-btn').disabled = false;
                    document.getElementById('comment-stop-btn').disabled = true;
                }
            }, maxRecordingTime);
        } catch (err) {
            alert('Error mengakses mikrofon: ' + err.message);
        }
    });

    document.getElementById('comment-stop-btn').addEventListener('click', () => {
        if (commentRecorder.state === 'recording') {
            commentRecorder.stop();
            clearTimeout(timeoutId);
        }
        document.getElementById('comment-record-btn').disabled = false;
        document.getElementById('comment-stop-btn').disabled = true;
    });
</script>

<style>
    /* Voice Note Styles */
    .voice-recorder-container {
        margin: 1.5rem 0;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .voice-controls {
        display: flex;
        gap: 0.75rem;
        margin: 1rem 0;
    }

    .audio-preview {
        width: 100%;
        margin-top: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: none;
    }

    .audio-info {
        display: block;
        margin-top: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .voice-note-player {
        margin: 1.5rem 0;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .voice-note-reply {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .voice-note-reply audio {
        width: 100%;
    }

    .audio-duration {
        display: block;
        text-align: right;
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    /* Existing Styles (tidak diubah) */
    .mindspace-post-container {
        padding: 2rem 0 4rem;
        background-color: #f8fafc;
    }

    .emotion-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .post-navigation {
        margin-bottom: 1.5rem;
    }

    .mindspace-post-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .post-header {
        margin-bottom: 1.5rem;
    }

    .post-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .post-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .author-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .profile-pic {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .author-details {
        display: flex;
        flex-direction: column;
    }

    .username {
        font-weight: 500;
        color: #1e293b;
    }

    .post-date {
        font-size: 0.85rem;
        color: #64748b;
    }

    .anonymous-badge {
        background-color: #f1f5f9;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .post-stats {
        display: flex;
        gap: 1.25rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
        color: #64748b;
    }

    .post-content {
        line-height: 1.8;
        color: #334155;
        margin-bottom: 1.5rem;
        white-space: pre-line;
        font-size: 1.05rem;
    }

    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .category-badge {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .post-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .edit-btn {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .edit-btn:hover {
        background-color: #bae6fd;
    }

    .delete-btn {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .delete-btn:hover {
        background-color: #fecaca;
    }

    .mindspace-replies-section {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .replies-title {
        font-size: 1.5rem;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .reply-count {
        color: #64748b;
        font-size: 1rem;
        font-weight: normal;
    }

    .reply-form-container {
        margin-bottom: 2rem;
    }

    .reply-form textarea {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        resize: none;
        margin-bottom: 1rem;
        min-height: 120px;
        transition: border-color 0.2s;
    }

    .reply-form textarea:focus {
        outline: none;
        border-color: #7dd3fc;
        box-shadow: 0 0 0 3px rgba(125, 211, 252, 0.2);
    }

    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox-group input {
        accent-color: #0ea5e9;
    }

    .checkbox-group label {
        font-size: 0.9rem;
        color: #64748b;
    }

    .login-prompt {
        text-align: center;
        padding: 1.5rem;
        background-color: #f1f5f9;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .login-prompt p {
        margin-bottom: 1rem;
        color: #475569;
    }

    .replies-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .reply-card {
        padding: 1.5rem;
        border-radius: 8px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .reply-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .reply-author {
        display: flex;
        flex-direction: column;
    }

    .reply-date {
        font-size: 0.8rem;
        color: #64748b;
    }

    .reply-content {
        color: #334155;
        line-height: 1.7;
        margin-left: 3.25rem;
    }

    .reply-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        margin-left: 3.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
    }

    .empty-icon {
        font-size: 2.5rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.2rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #64748b;
        max-width: 400px;
        margin: 0 auto;
    }

    @media (max-width: 640px) {
        .mindspace-post-card,
        .mindspace-replies-section {
            padding: 1.5rem;
        }

        .post-title {
            font-size: 1.5rem;
        }

        .post-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .reply-content,
        .reply-actions {
            margin-left: 0;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>