<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/user/dashboard.php');
}

$page_title = "Login";
require_once 'includes/header.php';

// Generate CSRF token
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi";
    } else {
        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_counselor'] = $user['is_counselor'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['is_anonymous'] = $user['is_anonymous'];

                // Tambahan: simpan counselor_id jika dia adalah konselor
                if ($user['is_counselor']) {
                    $uid = $user['user_id'];
                    $stmt = $conn->prepare("SELECT counselor_id FROM counselors WHERE user_id = ?");
                    $stmt->bind_param("i", $uid);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $counselor = $result->fetch_assoc();
                        $_SESSION['counselor_id'] = $counselor['counselor_id'];
                    } else {
                        error_log("User dengan user_id $uid adalah konselor, tapi tidak ditemukan di tabel counselors.");
                    }
                }

                // Debugging: Cek isi session
                error_log('Session: ' . print_r($_SESSION, true));
                
                // Log login
                log_action('login', "User ID {$user['user_id']} ({$user['username']}) berhasil login.");

                // Remember me cookie
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 60 * 60 * 24 * 30; // 30 hari
                    
                    setcookie('remember_token', $token, $expiry, '/');
                    
                    // Simpan token di database
                    $stmt = $conn->prepare("UPDATE users SET remember_token = ?, remember_token_expiry = ? WHERE user_id = ?");
                    $stmt->bind_param("ssi", $token, date('Y-m-d H:i:s', $expiry), $user['user_id']);
                    $stmt->execute();
                }
                
                // Redirect ke halaman yang diminta sebelumnya atau dashboard
                $redirect_url = $_SESSION['redirect_url'] ?? BASE_URL . '/user/dashboard.php';
                unset($_SESSION['redirect_url']);
                
                redirect($redirect_url, 'Login berhasil! Selamat datang kembali.');
            } else {
                $error = "Email atau password salah";
            }
        } else {
            $error = "Email atau password salah";
        }
    }
}

ob_end_flush(); // Menutup output buffering
?>

<!-- Form HTML -->
<div class="auth-container">
    <div class="auth-form">
        <h2>Login ke RuangEmosi</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group checkbox">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
        </form>
        
        <div class="auth-links">
            <p>Belum punya akun? <a href="<?php echo BASE_URL; ?>/register.php">Daftar disini</a></p>
            <p><a href="<?php echo BASE_URL; ?>/forgot-password.php">Lupa password?</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
