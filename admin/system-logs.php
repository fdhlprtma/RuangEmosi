<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_admin(); // Hanya admin yang bisa mengakses halaman ini

// Ambil semua log yang terbaru
$logs = $conn->query("SELECT * FROM system_logs ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = "Log Sistem";
require_once '../includes/header.php';
?>

<section class="system-logs">
    <div class="container">
        <h1>Log Sistem</h1>
        
        <div class="logs-list">
            <?php if (count($logs) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['description']) ?></td>
                                <td><?= date("Y-m-d H:i:s", strtotime($log['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada log yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.system-logs {
    padding: 40px 0;
}

.logs-list {
    margin-top: 30px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table th, .table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f4f4f4;
}

.table tbody tr:hover {
    background-color: #f9f9f9;
}
</style>

<?php require_once '../includes/footer.php'; ?>
