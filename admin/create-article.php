<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin();

// Proses pembuatan artikel
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $author_id = $_SESSION['user_id'];

    // Upload gambar
    if ($_FILES['featured_image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['featured_image']['tmp_name'];
        $file_name = $_FILES['featured_image']['name'];
        $file_size = $_FILES['featured_image']['size'];
        $file_type = $_FILES['featured_image']['type'];

        $upload_dir = '../assets/images/articles/';
        $upload_path = $upload_dir . basename($file_name);

        // Cek ekstensi gambar
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_exts)) {
            // Resize gambar jika terlalu besar (lebih dari 600px lebar)
            $image_info = getimagesize($file_tmp);
            if ($image_info && $image_info[0] > 600) {
                $src = imagecreatefromstring(file_get_contents($file_tmp));
                $width = 600;
                $height = ($image_info[1] / $image_info[0]) * 600;
                $resized = imagecreatetruecolor($width, $height);
                imagecopyresampled($resized, $src, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
                imagejpeg($resized, $upload_path, 85);
                imagedestroy($resized);
                imagedestroy($src);
            } else {
                move_uploaded_file($file_tmp, $upload_path);
            }
            $featured_image = basename($file_name);
        } else {
            $error_message = "Ekstensi file tidak diizinkan. Harus JPG, JPEG, PNG, atau GIF.";
        }
    } else {
        $featured_image = null; // Jika tidak ada gambar yang diupload
    }

    if (!isset($error_message)) {
        // Simpan artikel ke database
        $query = "
            INSERT INTO articles (title, slug, content, excerpt, featured_image, author_id, category, view_count, is_published)
            VALUES ('$title', '$slug', '$content', '$excerpt', '$featured_image', '$author_id', '$category', 0, 1)
        ";

        if ($conn->query($query)) {
            // Logging setelah berhasil buat artikel
            log_action('create_article', "Admin ID $author_id membuat artikel baru berjudul \"$title\".");

            redirect('manage-articles.php', 'Artikel berhasil dibuat');
        } else {
            $error_message = "Gagal membuat artikel. Coba lagi.";
        }
    }
}

$page_title = "Buat Artikel Baru";
require_once '../includes/header.php';
?>

<section class="create-article">
    <div class="container">
        <h1>Buat Artikel Baru</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" name="title" id="title" required class="form-control" placeholder="Judul Artikel">
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug" required class="form-control" placeholder="Slug Artikel (unique)">
            </div>

            <div class="form-group">
                <label for="content">Konten</label>
                <textarea name="content" id="content" required class="form-control" placeholder="Konten Artikel"></textarea>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea name="excerpt" id="excerpt" class="form-control" placeholder="Excerpt artikel"></textarea>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <select name="category" id="category" class="form-control">
                    <option value="depresi">Depresi</option>
                    <option value="kecemasan">Kecemasan</option>
                    <option value="self-care">Self-Care</option>
                    <option value="stres">Stres</option>
                    <option value="relationship">Hubungan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="featured_image">Gambar Unggulan</label>
                <input type="file" name="featured_image" id="featured_image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Artikel</button>
        </form>
    </div>
</section>

<style>
.create-article {
    padding: 40px 0;
}
.create-article .form-group {
    margin-bottom: 20px;
}
.create-article .form-group label {
    font-weight: bold;
}
.create-article .form-control {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
}
.create-article .btn {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
}
.create-article .btn:hover {
    background-color: #0056b3;
}
</style>

<?php require_once '../includes/footer.php'; ?>
