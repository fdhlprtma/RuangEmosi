<?php
include '../includes/config.php';
include '../includes/auth.php';
include '../includes/header.php';

// Verifikasi admin
if (!is_admin()) {
    redirect(BASE_URL . '/admin/dashboard.php', 'Akses ditolak');
}

$result = $conn->query("SELECT c.*, u.full_name, u.user_id 
                       FROM counselors c 
                       JOIN users u ON c.user_id = u.user_id");
?>

<section class="counselor-management">
    <div class="container">
        <h2 class="title">Manajemen Konselor</h2>
        <div class="table-responsive">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Spesialisasi</th>
                        <th>Tarif</th>
                        <th>Rating</th>
                        <th>Sesi</th>
                        <th>Sertifikat</th>
                        <?php if (is_admin()): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['specialization']) ?></td>
                            <td>Rp<?= number_format($row['hourly_rate'], 2) ?></td>
                            <td>
                                <?= $row['rating'] ?>/5<br>
                                <span class="star-rating">
                                    <?php
                                    $rounded = round($row['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rounded ? '★' : '☆';
                                    }
                                    ?>
                                </span>
                            </td>
                            <td><?= $row['session_count'] ?></td>
                            <td>
                                <?php if ($row['certificate_path']): ?>
                                    <a href="../<?= $row['certificate_path'] ?>" class="btn-view" target="_blank">Lihat</a>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <?php if (is_admin()): ?>
                                <td>
                                    <a href="delete_counselor.php?id=<?= $row['user_id'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus konselor ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
    .counselor-management {
        padding: 40px;
        font-family: 'Segoe UI', sans-serif;
    }

    .title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .styled-table thead {
        background-color: #2b6777;
        color: #fff;
    }

    .styled-table th,
    .styled-table td {
        padding: 15px;
        text-align: left;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f3f3f3;
    }

    .btn-view {
        background-color: #52ab98;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .btn-view:hover {
        background-color: #40877a;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #fff;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.85rem;
    }

    @media screen and (max-width: 768px) {

        .styled-table th,
        .styled-table td {
            padding: 10px;
        }

        .title {
            font-size: 22px;
        }
    }

    /* Tambahkan style berikut ke CSS existing */
    .btn-delete {
        background-color: #e74c3c;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-delete:hover {
        background-color: #c0392b;
    }

    .btn-delete i {
        font-size: 0.9rem;
    }

    .star-rating {
        color: #f5b301;
        font-size: 1.1rem;
    }
</style>

<?php include '../includes/footer.php'; ?>