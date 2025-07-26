<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_counselor();

$user_id = $_SESSION['user_id'];
$counselor = get_counselor_data($user_id);

// Proses form ketersediaan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = $_POST['days'] ?? [];
    $slots = [];

    // Validasi dan format data
    foreach ($days as $day => $times) {
        foreach ($times as $slot) {
            if (!empty($slot['start']) && !empty($slot['end'])) {
                $slots[] = [
                    'day' => $day,
                    'start' => $slot['start'],
                    'end' => $slot['end']
                ];
            }
        }
    }

    if (count($slots) > 0) {
        $conn->begin_transaction();
        try {
            $conn->query("DELETE FROM counselor_availability WHERE counselor_id = {$counselor['counselor_id']}");

            $stmt = $conn->prepare("INSERT INTO counselor_availability (counselor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");

            foreach ($slots as $slot) {
                $stmt->bind_param("isss", $counselor['counselor_id'], $slot['day'], $slot['start'], $slot['end']);
                $stmt->execute();
            }

            $conn->commit();
            $message = "Ketersediaan berhasil diperbarui!";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Gagal menyimpan ketersediaan: " . $e->getMessage();
        }
    } else {
        $error = "Tidak ada waktu yang valid untuk disimpan.";
    }
}

$availability = $conn->query("SELECT * FROM counselor_availability WHERE counselor_id = {$counselor['counselor_id']}")->fetch_all(MYSQLI_ASSOC);

$page_title = "Atur Ketersediaan";
require_once '../includes/header.php';
?>

<section class="availability">
    <div class="container">
        <h1>Atur Ketersediaan Konsultasi</h1>

        <?php if (!empty($message)) echo "<div class='success'>✅ $message</div>"; ?>
        <?php if (!empty($error)) echo "<div class='error'>❗ $error</div>"; ?>

        <form method="POST" id="availabilityForm">
            <div class="days-container">
                <?php
                $days = [
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu'
                ];

                foreach ($days as $key => $day):
                    $daySlots = array_filter($availability, fn($s) => $s['day_of_week'] === $key);
                ?>
                <div class="day-card">
                    <h3><?= $day ?></h3>
                    <div class="time-slots" data-day="<?= $key ?>">
                        <?php if (!empty($daySlots)): ?>
                            <?php foreach (array_values($daySlots) as $index => $slot): ?>
                                <div class="time-slot">
                                    <input type="time" name="days[<?= $key ?>][<?= $index ?>][start]" value="<?= substr($slot['start_time'], 0, 5) ?>">
                                    <span>sampai</span>
                                    <input type="time" name="days[<?= $key ?>][<?= $index ?>][end]" value="<?= substr($slot['end_time'], 0, 5) ?>">
                                    <button type="button" class="remove-slot">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="time-slot">
                                <input type="time" name="days[<?= $key ?>][0][start]">
                                <span>sampai</span>
                                <input type="time" name="days[<?= $key ?>][0][end]">
                                <button type="button" class="remove-slot">&times;</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="add-slot" data-day="<?= $key ?>">
                        <i class="fas fa-plus"></i> Tambah Slot
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Ketersediaan</button>
        </form>
    </div>
</section>

<style>
.availability {
    padding: 40px 0;
}
.days-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 30px 0;
}
.day-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.time-slot {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0;
}
.time-slot input[type="time"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.remove-slot {
    background: #ff4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
}
.add-slot {
    margin-top: 10px;
    background: none;
    border: none;
    color: var(--primary-color);
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-slot').forEach(button => {
        button.addEventListener('click', function() {
            const day = this.dataset.day;
            const container = this.previousElementSibling;
            const index = container.querySelectorAll('.time-slot').length;

            const newSlot = document.createElement('div');
            newSlot.className = 'time-slot';
            newSlot.innerHTML = `
                <input type="time" name="days[${day}][${index}][start]" required>
                <span>sampai</span>
                <input type="time" name="days[${day}][${index}][end]" required>
                <button type="button" class="remove-slot">&times;</button>
            `;
            container.appendChild(newSlot);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slot')) {
            e.target.closest('.time-slot').remove();
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
