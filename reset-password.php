<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "Reset Password";
require_once 'includes/header.php';

$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';

// Validasi token
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT user_id FROM users 
                           WHERE reset_token = ? 
                           AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $token_error = "Token tidak valid atau sudah kadaluarsa";
        $token = '';
        log_reset_request(0, '', 'failed', $token);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    verify_csrf_token();
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password
    if (strlen($password) < 8) {
        $error = "Password minimal 8 karakter";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak sama";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Update password dan log
        $stmt = $conn->prepare("UPDATE users SET 
                              password = ?, 
                              reset_token = NULL, 
                              reset_token_expiry = NULL,
                              reset_attempts = 0 
                              WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        
        if ($stmt->execute()) {
            // Get user info for logging
            $stmt = $conn->prepare("SELECT user_id, email FROM users WHERE reset_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            log_reset_request($user['user_id'], $user['email'], 'used', $token);
            redirect(BASE_URL . '/login.php', 'Password berhasil direset. Silakan login');
        } else {
            log_reset_request(0, '', 'failed', $token);
            $error = "Gagal mereset password. Silakan coba lagi.";
        }
    }
}
?>

<!-- Tampilan sama seperti sebelumnya, tambahkan logging di bagian error -->