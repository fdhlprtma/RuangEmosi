<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

$user_id = $_SESSION['user_id'];
$counselor = get_counselor_data($user_id);

$consultations = $conn->query("
   SELECT c.*, u.username
FROM consultations c
JOIN users u ON c.user_id = u.user_id
WHERE c.counselor_id = {$counselor['counselor_id']}
ORDER BY c.schedule DESC
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Daftar Konsultasi";

require_once '../includes/header.php';
?>

<?php if (isset($_SESSION['message'])): ?>
  <div class="alert alert-success"><?= $_SESSION['message']; ?></div>
  <?php unset($_SESSION['message']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<section class="session-reports">
  <div class="container">
    <h1>Daftar Konsultasi</h1>

    <?php if (empty($consultations)): ?>
      <p>Tidak ada sesi konsultasi.</p>
    <?php else: ?>
      <div class="reports-table">
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Klien</th>
              <th>Durasi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultations as $consultation): ?>
              <tr>
                <td><?= date('d M Y ', strtotime($consultation['schedule'])) ?></td>
                <td><?= htmlspecialchars($consultation['username']) ?></td>
                <td><?= $consultation['duration'] ?> menit</td>
                <td>
                  <span class="status-badge <?= htmlspecialchars($consultation['status']) ?>">
                    <?= ucfirst($consultation['status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($consultation['status'] === 'confirmed' || $consultation['status'] === 'accepted'): ?>
                    <form method="POST" action="mark_completed.php" style="display:inline;">
                      <input type="hidden" name="id" value="<?= $consultation['consultation_id'] ?>">
                      <button type="submit" class="btn btn-success">Tandai Selesai</button>
                    </form>
                  <?php elseif ($consultation['status'] === 'completed'): ?>
                    <span class="badge badge-success">Selesai</span>
                  <?php elseif ($consultation['status'] === 'rejected'): ?>
                    <span class="badge badge-danger">Ditolak</span>
                  <?php else: ?>
                    <span class="text-muted">Tidak dapat ditandai</span>
                  <?php endif; ?>

                  <?php if ($consultation['status'] === 'pending'): ?>
                    <button type="button" class="btn btn-small open-accept-modal" data-id="<?= $consultation['consultation_id'] ?>">Terima</button>
                    <form method="POST" action="update_status.php" style="display:inline;">
                      <input type="hidden" name="id" value="<?= $consultation['consultation_id'] ?>">
                      <input type="hidden" name="action" value="rejected">
                      <button type="submit" class="btn btn-small">Tolak</button>
                    </form>

                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Modal Terima Konsultasi -->
<div id="acceptModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Konfirmasi Terima Konsultasi</h3>
    <form method="POST" action="update_status.php">
      <input type="hidden" name="id" id="consultationId">
      <input type="hidden" name="action" value="accepted">
      <div>
        <label>Link Zoom / Google Meet:</label>
        <input type="url" name="meeting_link" required placeholder="https://zoom.us/..." style="width:100%;">
      </div>
      <div>
        <label>Nomor WhatsApp:</label>
        <input type="text" name="number_phone" required placeholder="08xxx" style="width:100%;">
      </div>
      <div style="margin-top:10px;">
        <label>Catatan untuk Klien:</label>
        <textarea name="notes" rows="3" placeholder="Tulis catatan atau instruksi..." style="width:100%;"></textarea>
      </div>
      <div style="margin-top:15px;">
        <button type="submit" class="btn btn-primary">Kirim</button>
      </div>
    </form>
  </div>
</div>

<style>
  .session-reports {
    padding: 40px 0;
  }

  .reports-table table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
  }

  .reports-table th,
  .reports-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
  }

  .status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    text-transform: capitalize;
  }

  .status-badge.pending {
    background: #fff3cd;
    color: #856404;
  }

  .status-badge.confirmed {
    background: #cce5ff;
    color: #004085;
  }

  .status-badge.completed {
    background: #d4edda;
    color: #155724;
  }

  .status-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
  }

  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
  }

  .modal-content {
    background: white;
    margin: 10% auto;
    padding: 20px;
    width: 70%;
    border-radius: 8px;
  }
</style>

<script>
  const acceptModal = document.getElementById("acceptModal");
  const closeBtns = document.querySelectorAll(".modal .close");

  document.querySelectorAll('.open-accept-modal').forEach(button => {
    button.addEventListener('click', () => {
      const consultationId = button.getAttribute('data-id');
      document.getElementById('consultationId').value = consultationId;
      acceptModal.style.display = "block";
    });
  });

  closeBtns.forEach(btn => {
    btn.onclick = () => {
      btn.closest(".modal").style.display = "none";
    };
  });

  window.onclick = (e) => {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = "none";
    }
  };
</script>

<?php require_once '../includes/footer.php'; ?>