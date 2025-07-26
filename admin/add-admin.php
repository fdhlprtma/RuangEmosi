<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi data
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO users 
                          (username, email, password, is_admin) 
                          VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $email, $password, $is_admin);
    
    if ($stmt->execute()) {
        redirect('manage-users.php', 'Admin berhasil ditambahkan');
    }
}

$page_title = "Kelola Admin";
require_once '../includes/header.php';
?>

<section class="admin-users">
    <div class="container">
        <h1>Tambah Admin Baru</h1>
        
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" name="is_admin" id="is_admin" checked>
                    <label for="is_admin">Akses Admin</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Tambah Admin</button>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>