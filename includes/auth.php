<?php
require_once 'config.php';
require_once 'functions.php';  // Pastikan hanya sekali

// Memeriksa apakah user sudah login
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect(BASE_URL . '/login.php', 'Silakan login untuk mengakses halaman ini.');
    }
}

// Memeriksa apakah user adalah counselor
function require_counselor() {
    require_login();
    if (!is_counselor()) {
        redirect(BASE_URL . '/', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

// Memeriksa apakah user adalah admin
function require_admin() {
    require_login();
    if (!function_exists('is_admin')) {
        function is_admin() {
            // Replace this with your actual logic to check if the user is an admin
            return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        }
    }

    if (!is_admin() && !is_counselor()) {
        redirect(BASE_URL . '/', 'Anda tidak memiliki akses ke halaman ini.');
    }
}

// Tidak perlu mendeklarasikan ulang fungsi verify_csrf_token di sini, karena sudah ada di functions.php
?>
