<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'functions.php';


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuangEmosi - <?php echo $page_title ?? 'Ruang Aman untuk Kesehatan Mental'; ?></title>
    <link rel="icon" href="<?php echo BASE_URL; ?>/assets/images/pictures/logo1.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .main-header {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 300;
            font-size: 1.0rem;
            color: #0d47a1;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .main-nav ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .main-nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .main-nav .btn-primary {
            background-color: #0d47a1;
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
        }

        .mobile-menu-toggle {
            display: none;
        }

        .hero {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            padding: 4rem 1rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2rem;
        }

        .hero-content {
            flex: 1 1 50%;
        }

        .hero h1 {
            font-size: 2.5rem;
            color: #0d47a1;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #0d47a1;
            color: #fff;
        }

        .btn-secondary {
            background-color: #e0e0e0;
            color: #333;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">
                    <img src="<?php echo BASE_URL; ?>../assets/images/pictures/logo1.png" alt="RuangEmosi Logo">
                    <span>RuangEmosi</span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php if (is_admin()): ?>
                        <li><a href="<?= BASE_URL ?>/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>

                    <?php if (is_counselor() && !is_admin() && is_logged_in()): ?>
                        <li><a href="<?= BASE_URL ?>/counselor/dashboard.php">Konselor</a></li>
                    <?php endif; ?>

                    <li><a href="<?php echo BASE_URL; ?>">Beranda</a></li>

                    <?php if (!is_admin()): ?>
                        <li><a href="<?php echo BASE_URL; ?>/consultation">Konseling</a></li>
                    <?php endif; ?>

                    <?php if (!is_admin()): ?>
                        <li><a href="<?php echo BASE_URL; ?>/tests/phq9.php">Tes Mental</a></li>
                    <?php endif; ?>

                    <li><a href="<?php echo BASE_URL; ?>/articles/index.php">Artikel</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/forum">Forum</a></li>


                    <?php if (is_logged_in() && !is_admin() && !is_counselor()): ?>
                        <li><a href="<?php echo BASE_URL; ?>/user/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>

                    <?php if (is_logged_in()): ?>
                        <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <main class="container">
        <?php display_message(); ?>
        <!-- Isi konten halaman di sini -->
    </main>

</body>

</html>

<?php
ob_end_flush();
?>