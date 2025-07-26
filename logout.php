<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Hapus semua data session
$_SESSION = array();

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hapus remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
    
    // Jika user login, hapus token dari database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL, remember_token_expiry = NULL WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
    }
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
redirect(BASE_URL . '/login.php', 'Anda telah logout.');
?>