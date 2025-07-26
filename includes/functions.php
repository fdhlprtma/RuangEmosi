<?php
// Memulai output buffering untuk memastikan tidak ada output sebelum header
ob_start();
require_once 'config.php';

// Fungsi untuk redirect dengan pesan
function redirect($location, $message = null)
{
    // Memulai output buffering
    ob_start();  

    if ($message) {
        $_SESSION['message'] = $message;
    }

    // Pastikan header belum dikirim
    if (!headers_sent()) {
        header("Location: $location");
        exit();
    } else {
        echo "<script>window.location.href='$location';</script>";
        exit();
    }

    // Mengakhiri output buffering dan membersihkan output
    ob_end_flush();
}

// Fungsi untuk menampilkan pesan
function display_message()
{
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

// Fungsi untuk sanitasi input
function sanitize($data)
{
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// Fungsi untuk mengecek login
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}


// Fungsi untuk mengecek role user
function is_counselor()
{
    return isset($_SESSION['is_counselor']) && $_SESSION['is_counselor'];
}

// Fungsi untuk hash password
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

// Fungsi untuk verifikasi password
function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

// Fungsi untuk mendapatkan data user
function get_user_data($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function get_counselor_data($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM counselors WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function get_logged_in_user_id() {
    // Pastikan session sudah dimulai
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null; // Ambil user_id dari session
}

// Fungsi untuk mengecek apakah user adalah admin
function is_admin()
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function require_user() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

// Fungsi untuk menampilkan waktu dalam format "x waktu yang lalu"
if (!function_exists('time_ago')) {
    function time_ago($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // Menghitung minggu secara manual
        $weeks = floor($diff->d / 7);
        $diff->d -= $weeks * 7;

        // Menyusun array dengan unit waktu yang relevan
        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );

        // Menambahkan minggu ke dalam array jika ada
        if ($weeks > 0) {
            $string['w'] = 'minggu'; // Menambahkan key untuk minggu
        }

        // Memproses perhitungan waktu dan membentuk string output
        foreach ($string as $k => &$v) {
            if ($k == 'w' && $weeks > 0) {
                $v = $weeks . ' ' . $v . ($weeks > 1 ? 's' : ''); // Menangani plural
            } elseif ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }
}

// Fungsi untuk memeriksa remember me cookie
function check_remember_me()
{
    global $conn;

    if (!is_logged_in() && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        $stmt = $conn->prepare("SELECT user_id, username, email, is_counselor, is_admin, is_anonymous FROM users 
                               WHERE remember_token = ? AND remember_token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_counselor'] = $user['is_counselor'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_anonymous'] = $user['is_anonymous'];
        }
    }
}

// Fungsi untuk mencatat log
function log_action($action, $description) {
    global $conn;
    
    // Jika ada session user_id yang aktif
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Persiapkan query untuk menyertakan user_id
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $description);
    $stmt->execute();
    $stmt->close();
}

// Fungsi untuk resize gambar
function resize_image($source_path, $destination_path, $max_width = 800, $quality = 75) {
    list($width, $height, $type) = getimagesize($source_path);

    $ratio = $width / $height;
    $new_width = $max_width;
    $new_height = $max_width / $ratio;

    $src_image = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($source_path);
            break;
        default:
            return false;
    }

    $dst_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Simpan sebagai JPG saja agar lebih ringan
    imagejpeg($dst_image, $destination_path, $quality);

    imagedestroy($src_image);
    imagedestroy($dst_image);

    return true;
}

// Panggil fungsi check_remember_me di awal
check_remember_me();

// =========================
// CSRF Token Functions
// =========================

// Fungsi untuk generate CSRF token
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi untuk verifikasi CSRF token
function verify_csrf_token()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('CSRF token tidak valid.');
        }
    }
}

ob_end_flush();
?>
