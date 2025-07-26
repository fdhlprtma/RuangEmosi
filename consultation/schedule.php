<?php
session_start();
include '../includes/config.php';
include '../includes/header.php';

$counselor_id = $_GET['counselor_id'] ?? null;

if (!$counselor_id) {
    echo "Konselor tidak ditemukan.";
    exit;
}

// Ambil data konselor + nama dari tabel users
$stmt = $conn->prepare("
    SELECT c.*, u.full_name 
    FROM counselors c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.counselor_id = ?
");
$stmt->bind_param("i", $counselor_id);
$stmt->execute();
$counselor = $stmt->get_result()->fetch_assoc();

if (!$counselor) {
    echo "Konselor tidak ditemukan.";
    exit;
}
?>

<!-- Membungkus style hanya untuk halaman schedule.php -->
<style>
    /* Hanya berlaku di halaman schedule */
    #schedule-page {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f2f4f8;
        margin: 0;
        padding: 0;
    }

    #schedule-page .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    #schedule-page .counselor-header {
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    #schedule-page .counselor-header img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #2e89ff;
    }

    #schedule-page .counselor-info h2 {
        margin: 0;
        font-size: 26px;
        color: #333;
    }

    #schedule-page .counselor-info p {
        margin: 6px 0;
        color: #555;
        font-size: 15px;
    }

    #schedule-page form {
        margin-top: 30px;
    }

    #schedule-page form label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        margin-top: 20px;
        color: #333;
    }

    #schedule-page form input[type="date"],
    #schedule-page form input[type="time"] {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
    }

    #schedule-page form button {
        margin-top: 30px;
        width: 100%;
        padding: 14px;
        background-color: #2e89ff;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #schedule-page form button:hover {
        background-color: #156fd0;
    }

    @media (max-width: 768px) {
        #schedule-page .counselor-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        #schedule-page .counselor-info h2 {
            font-size: 22px;
        }

        #schedule-page .container {
            margin: 20px;
        }
    }
</style>

<!-- Membungkus halaman schedule dalam ID khusus -->
<div id="schedule-page">
    <div class="container">
        <div class="counselor-header">
            <img src="../<?= $counselor['photo'] ?>" alt="Foto Konselor">
            <div class="counselor-info">
                <h2><?= $counselor['full_name'] ?></h2>
                <p><strong>Spesialisasi:</strong> <?= $counselor['specialization'] ?></p>
                <p><strong>Tarif:</strong> Rp <?= number_format($counselor['hourly_rate'], 0, ',', '.') ?>/jam</p>
            </div>
        </div>

        <form method="POST" action="schedule_action.php">
            <input type="hidden" name="counselor_id" value="<?= $counselor_id ?>">

            <label for="schedule">Tanggal Konsultasi</label>
            <input type="date" name="schedule" required>

            <label for="start_time">Waktu Mulai</label>
            <input type="time" name="start_time" required>

            <label for="end_time">Waktu Selesai</label>
            <input type="time" name="end_time" required>

            <button type="submit">Ajukan Jadwal</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
