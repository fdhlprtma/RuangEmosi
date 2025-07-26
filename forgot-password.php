<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'vendor/autoload.php'; // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$page_title = "Lupa Password";
require_once 'includes/header.php';

// Logging function
function log_reset_request($user_id, $email, $status, $token = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO password_reset_logs 
                          (user_id, email, ip_address, action_time, token, status, user_agent)
                          VALUES (?, ?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param("isssss", 
        $user_id,
        $email,
        $_SERVER['REMOTE_ADDR'],
        $token,
        $status,
        $_SERVER['HTTP_USER_AGENT']
    );
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CAPTCHA first
    $captcha = $_POST['g-recaptcha-response'] ?? '';
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $captcha,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response);
    
    if (!$result->success || $result->score < 0.5) {
        $error = "Verifikasi CAPTCHA gagal. Silakan coba lagi.";
        log_reset_request(0, $_POST['email'] ?? '', 'failed');
        $conn->query("UPDATE users SET reset_attempts = reset_attempts + 1 WHERE email = '".$conn->real_escape_string($_POST['email'])."'");
    } else {
        // Proses reset password
        $email = sanitize($_POST['email']);
        
        $stmt = $conn->prepare("SELECT user_id, email, reset_attempts FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Rate limiting
            if ($user['reset_attempts'] > 5) {
                $error = "Terlalu banyak percobaan reset password. Silakan coba lagi nanti.";
                log_reset_request($user['user_id'], $email, 'failed');
                redirect(BASE_URL . '/forgot-password.php', $error);
            }
            
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + 3600);
            
            // Update database
            $stmt = $conn->prepare("UPDATE users SET 
                                  reset_token = ?, 
                                  reset_token_expiry = ?, 
                                  reset_attempts = reset_attempts + 1 
                                  WHERE user_id = ?");
            $stmt->bind_param("ssi", $token, $expiry, $user['user_id']);
            
            if ($stmt->execute()) {
                // Kirim email
                $mail = new PHPMailer(true);
                
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USER;
                    $mail->Password = SMTP_PASS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = SMTP_PORT;
                    
                    // Recipients
                    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                    $mail->addAddress($email);
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password - RuangEmosi';
                    $mail->Body = "
                        <h2>Reset Password</h2>
                        <p>Silakan klik link berikut untuk reset password:</p>
                        <p><a href='".BASE_URL."/reset-password.php?token=$token'>Reset Password</a></p>
                        <p>Link akan kadaluarsa dalam 1 jam</p>
                    ";
                    
                    $mail->send();
                    log_reset_request($user['user_id'], $email, 'requested', $token);
                    redirect(BASE_URL . '/forgot-password.php', 'Link reset telah dikirim ke email Anda');
                } catch (Exception $e) {
                    error_log("Email Error: ".$mail->ErrorInfo);
                    log_reset_request($user['user_id'], $email, 'failed', $token);
                    $error = "Gagal mengirim email. Silakan coba lagi nanti.";
                }
            }
        } else {
            log_reset_request(0, $email, 'failed');
            $error = "Email tidak terdaftar";
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Lupa Password</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <!-- Google reCAPTCHA -->
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block g-recaptcha" 
                    data-sitekey="<?= RECAPTCHA_SITE_KEY ?>" 
                    data-callback='onSubmit' 
                    data-action='submit'>
                    Kirim Link Reset
                </button>
            </div>
        </form>
        
        <div class="auth-links">
            <a href="<?= BASE_URL ?>/login.php">Ingat password? Login disini</a>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=<?= RECAPTCHA_SITE_KEY ?>"></script>
<script>
function onSubmit(token) {
    document.getElementById("g-recaptcha-response").value = token;
    document.forms[0].submit();
}
</script>

<?php require_once 'includes/footer.php'; ?>