<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/RuangEmosi/includes/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/RuangEmosi/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/RuangEmosi/includes/auth.php';

require_login();

$page_title = "Buat Post Baru";
require_once $_SERVER['DOCUMENT_ROOT'] . '/RuangEmosi/includes/header.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category = sanitize($_POST['category']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    // Upload Gambar
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $image_dir = "assets/images/pictures/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $image_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/RuangEmosi/" . $target_file)) {
            $image_path = $target_file;
        }
    }

    // Voice Note
    $voice_path = null;
    if (!empty($_POST['voice_note_path'])) {
        $voice_dir = "assets/voices/";
        $voice_name = time() . ".webm";
        $voice_file = $_SERVER['DOCUMENT_ROOT'] . "/RuangEmosi/" . $voice_dir . $voice_name;

        file_put_contents($voice_file, base64_decode($_POST['voice_note_path']));
        $voice_path = $voice_dir . $voice_name;
    }

    // Validasi
    if (empty($title) || (empty($content) && !$voice_path)) {
        $error = "Judul dan isi teks atau voice note harus diisi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO forum_posts (user_id, title, content, is_anonymous, category, image_path, voice_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississs", $user_id, $title, $content, $is_anonymous, $category, $image_path, $voice_path);

        if ($stmt->execute()) {
            $post_id = $stmt->insert_id;
            redirect(BASE_URL . "/forum/post.php?id=$post_id", "Post berhasil dibuat!");
        } else {
            $error = "Gagal membuat post. Silakan coba lagi.";
        }
    }
}
?>

<section class="create-post">
    <div class="container">
        <div class="create-post-header">
            <h2>Buat Post Baru</h2>
            <a href="<?php echo BASE_URL; ?>/forum" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Forum
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="curhat">Curhat</option>
                    <option value="tanya">Tanya</option>
                    <option value="dukungan">Dukungan</option>
                    <option value="cerita">Cerita</option>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Konten Teks</label>
                <textarea id="content" name="content" rows="8" placeholder="Boleh dikosongkan jika memakai voice note"></textarea>
            </div>

            <div class="form-group">
                <label>Voice Note</label>
                <div class="voice-recorder">
                    <button type="button" id="record-btn" class="btn btn-primary">
                        <i class="fas fa-microphone"></i> Mulai Rekam
                    </button>
                    <button type="button" id="stop-btn" class="btn btn-danger" disabled>
                        <i class="fas fa-stop"></i> Stop Rekam
                    </button>
                    <audio id="audio-preview" controls class="mt-2"></audio>
                    <input type="hidden" name="voice_note_path" id="voice_note_path">
                </div>
            </div>

            <div class="form-group">
                <label for="image">Unggah Gambar</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small class="form-text">Maksimal ukuran file: 2MB</small>
            </div>

            <?php if ($_SESSION['is_anonymous']): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" checked>
                    <label class="form-check-label" for="is_anonymous">Posting sebagai Anonim</label>
                </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Publikasikan Post
                </button>
            </div>
        </form>
    </div>
</section>

<script>
let recorder;
let audioChunks = [];

document.getElementById('record-btn').addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recorder = new MediaRecorder(stream);
        
        recorder.ondataavailable = (e) => {
            audioChunks.push(e.data);
        };
        
        recorder.onstop = async () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            document.getElementById('audio-preview').src = audioUrl;
            
            const reader = new FileReader();
            reader.onloadend = () => {
                document.getElementById('voice_note_path').value = reader.result.split(',')[1];
            };
            reader.readAsDataURL(audioBlob);
        };
        
        recorder.start();
        document.getElementById('record-btn').disabled = true;
        document.getElementById('stop-btn').disabled = false;
        audioChunks = [];
    } catch (err) {
        alert('Error accessing microphone: ' + err.message);
    }
});

document.getElementById('stop-btn').addEventListener('click', () => {
    recorder.stop();
    document.getElementById('record-btn').disabled = false;
    document.getElementById('stop-btn').disabled = true;
});
</script>

<style>
    .voice-recorder {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

#audio-preview {
    width: 100%;
    margin-top: 1rem;
}
</style>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/RuangEmosi/includes/footer.php'; ?>