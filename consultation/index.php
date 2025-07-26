<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

// Pesan sukses atau error
if (isset($_SESSION['success'])) {
    echo '<div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

$query = "
    SELECT c.counselor_id, c.specialization, c.hourly_rate, c.photo,
           u.full_name, AVG(con.rating) AS average_rating
    FROM counselors c
    JOIN users u ON c.user_id = u.user_id
    LEFT JOIN consultations con ON c.counselor_id = con.counselor_id AND con.rating IS NOT NULL
    GROUP BY c.counselor_id
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Konselor - RuangEmosi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .page-header {
            text-align: center;
            padding: 40px 20px 20px;
        }

        .page-header h1 {
            font-size: 32px;
            color: #1e3a8a;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #64748b;
            font-size: 16px;
        }

        .counselor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #e0e7ff;
        }

        .name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 5px;
        }

        .spec {
            font-size: 14px;
            color: #475569;
            margin-bottom: 10px;
        }

        .price {
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
        }

        .rating {
            font-size: 14px;
            color: #f59e0b;
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #1e40af;
        }

        @media (max-width: 600px) {
            .card {
                padding: 15px;
            }

            .name {
                font-size: 16px;
            }

            .spec {
                font-size: 13px;
            }

            .price,
            .rating {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <div class="page-header">
        <h1>Konselor Terverifikasi</h1>
        <p>Pilih konselor yang sesuai dengan kebutuhan emosionalmu</p>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="counselor-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="../<?= htmlspecialchars($row['photo']) ?>" alt="Foto Konselor" class="profile-pic">
                    <div class="name"><?= htmlspecialchars($row['full_name']) ?></div>
                    <div class="spec"><?= htmlspecialchars($row['specialization']) ?></div>
                    <div class="price">Rp <?= number_format($row['hourly_rate'], 0, ',', '.') ?>/jam</div>
                    <?php if ($row['average_rating']): ?>
                        <div class="rating">⭐ <?= number_format($row['average_rating'], 1) ?>/5</div>
                    <?php else: ?>
                        <div class="rating" style="color: #9ca3af">Belum ada rating</div>
                    <?php endif; ?>
                    <a href="schedule.php?counselor_id=<?= $row['counselor_id'] ?>" class="btn">Buat Janji</a>
                    <a href="review.php?counselor_id=<?= $row['counselor_id'] ?>" class="btn" style="background:#10b981; margin-top: 10px;">Lihat Ulasan</a>

                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: gray;">Tidak ada konselor ditemukan.</p>
    <?php endif; ?>

    <?php include '../includes/footer.php'; ?>
</body>

</html>