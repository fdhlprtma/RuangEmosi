<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Pastikan pengguna sudah login
if (!is_logged_in()) {
    redirect(BASE_URL . '/login.php', 'Silakan login untuk melihat status konsultasi.');
}

$user_id = $_SESSION['user_id'];

// Ambil daftar konsultasi yang telah dijadwalkan oleh pengguna
$stmt = $conn->prepare("
    SELECT c.*, u.username AS counselor_name, cs.schedule, cs.start_time, cs.end_time, cs.status
    FROM consultations cs
    JOIN counselors c ON cs.counselor_id = c.counselor_id
    JOIN users u ON c.user_id = u.user_id
    WHERE cs.user_id = ?
    ORDER BY cs.schedule DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php require_once '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Konsultasi</title>
    <style>
        /* CSS untuk bagian status konsultasi */
        .status-section {
            padding: 40px 20px;
            background-color: #fafafa;
        }

        .status-section .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .status-section h2 {
            text-align: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Styling untuk tabel status */
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .status-table th,
        .status-table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .status-table th {
            background-color: #4CAF50;
            color: white;
        }

        .status-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-table td {
            font-size: 14px;
        }

        /* Styling untuk status */
        .status-table .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-table .status-confirmed {
            color: green;
            font-weight: bold;
        }

        .status-table .status-completed {
            color: blue;
            font-weight: bold;
        }

        .status-table .status-cancelled {
            color: red;
            font-weight: bold;
        }

        /* Styling untuk tombol */
        .status-section .btn-back {
            display: inline-block;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .status-section .btn-back:hover {
            background-color: #0056b3;
        }

        /* Responsif untuk tampilan mobile */
        @media (max-width: 768px) {
            .status-table {
                font-size: 14px;
            }

            .status-table th,
            .status-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<section class="status-section">
    <div class="container">
        <div class="profile-header">
            <h2>Status Konsultasi</h2>
            <a href="<?php echo BASE_URL; ?>/user/dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Konselor</th>
                        <th>Jadwal</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($consultation = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consultation['counselor_name']); ?></td>
                            <td><?php echo date("d-m-Y", strtotime($consultation['schedule'])); ?></td>
                            <td><?php echo date("H:i", strtotime($consultation['start_time'])); ?></td>
                            <td><?php echo date("H:i", strtotime($consultation['end_time'])); ?></td>
                            <td>
                                <?php 
                                if ($consultation['status'] == 'pending') {
                                    echo "<span class='status-pending'>Menunggu Konfirmasi</span>";
                                } elseif ($consultation['status'] == 'confirmed') {
                                    echo "<span class='status-confirmed'>Dikonfirmasi</span>";
                                } elseif ($consultation['status'] == 'completed') {
                                    echo "<span class='status-completed'>Selesai</span>";
                                } else {
                                    echo "<span class='status-cancelled'>Dibatalkan</span>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Anda belum memiliki jadwal konsultasi yang dijadwalkan.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>

</body>
</html>
