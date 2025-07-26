<?php
// Koneksi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ruang_emosi');


// Set session lifetime sebelum session_start()
$session_lifetime = 3600;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', $session_lifetime);
    session_set_cookie_params($session_lifetime);
    session_start();
}

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL
define('BASE_URL', 'http://localhost/ruangemosi');

// Regenerate session ID untuk mencegah fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

// reCAPTCHA Config
define('RECAPTCHA_SITE_KEY', 'your-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');

// Email Config
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-email-password');
define('EMAIL_FROM', 'noreply@ruangemosi.id');
define('EMAIL_FROM_NAME', 'RuangEmosi');

define('SITE_NAME', 'RuangEmosi');
define('CONTACT_EMAIL', 'teamsupport@ruangemosi.id');
define('COMPANY_ADDRESS', 'Makassar');
define('CONTACT_PHONE', '+62 123 4567 890');