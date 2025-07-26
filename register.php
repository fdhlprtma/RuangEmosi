<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/user/dashboard.php');
}

$page_title = "Daftar";
require_once 'includes/header.php';

// Generate CSRF token
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validasi
    $errors = [];
    
    if (strlen($username) < 5) {
        $errors[] = "Username minimal 5 karakter";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak sama";
    }
    
    // Cek username dan email unik
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Username atau email sudah digunakan";
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, is_anonymous) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $email, $hashed_password, $full_name, $is_anonymous);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['is_anonymous'] = $is_anonymous;
            $_SESSION['is_counselor'] = false;
            $_SESSION['is_admin'] = false;
            
            // Redirect ke dashboard
            redirect(BASE_URL . '/user/dashboard.php', 'Pendaftaran berhasil! Selamat datang di RuangEmosi.');
        } else {
            $errors[] = "Terjadi kesalahan. Silakan coba lagi";
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Daftar Akun Baru</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="5">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="full_name">Nama Lengkap (opsional)</label>
                <input type="text" id="full_name" name="full_name">
            </div>
            
            <div class="form-group checkbox">
                <input type="checkbox" id="is_anonymous" name="is_anonymous" checked>
                <label for="is_anonymous">Gunakan mode anonim (username tidak akan ditampilkan di forum)</label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </div>
        </form>
        
        <div class="auth-links">
            <p>Sudah punya akun? <a href="<?php echo BASE_URL; ?>/login.php">Login disini</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>