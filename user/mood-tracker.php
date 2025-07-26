<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');
$current_month = date('m');
$current_year = date('Y');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = sanitize($_POST['date']);
    $mood = sanitize($_POST['mood']);
    // $notes = sanitize($_POST['notes']);

    // Check if entry already exists
    $stmt = $conn->prepare("SELECT entry_id FROM mood_tracker WHERE user_id = ? AND date = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $entry = $result->fetch_assoc();
        $stmt = $conn->prepare("UPDATE mood_tracker SET mood = ?, notes = ? WHERE entry_id = ?");
        $stmt->bind_param("ssi", $mood, $notes, $entry['entry_id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO mood_tracker (user_id, mood, notes, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $mood, $notes, $date);
    }

    if ($stmt->execute()) {
        $message = "Catatan mood berhasil disimpan!";
    } else {
        $error = "Gagal menyimpan catatan mood.";
    }
}

// Get selected month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)$current_month;
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)$current_year;

// Adjust month navigation logic
if ($month < 1) {
    $month = 12;
    $year -= 1;
} elseif ($month > 12) {
    $month = 1;
    $year += 1;
}

$month = max(1, min(12, $month));
$year = max(2020, min(2100, $year));

// Get mood data for selected month
$start_date = "$year-$month-01";
$end_date = date('Y-m-t', strtotime($start_date));

$stmt = $conn->prepare("SELECT date, mood, notes FROM mood_tracker 
                       WHERE user_id = ? AND date BETWEEN ? AND ?
                       ORDER BY date DESC");
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$mood_entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Create calendar
$calendar = [];
$days_in_month = date('t', strtotime($start_date));
$first_day = date('N', strtotime($start_date));

for ($i = 1; $i < $first_day; $i++) {
    $calendar[] = ['day' => null];
}

for ($day = 1; $day <= $days_in_month; $day++) {
    $date = date('Y-m-d', strtotime("$year-$month-$day"));
    $entry = array_filter($mood_entries, fn($e) => $e['date'] === $date);
    $calendar[] = [
        'day' => $day,
        'date' => $date,
        'mood' => $entry ? reset($entry)['mood'] : null,
        'notes' => $entry ? reset($entry)['notes'] : null
    ];
}

$mood_options = [
    'sangat_bahagia' => ['label' => 'Sangat Bahagia', 'emoji' => '😄', 'color' => '#4CAF50'],
    'bahagia' => ['label' => 'Bahagia', 'emoji' => '😊', 'color' => '#8BC34A'],
    'netral' => ['label' => 'Netral', 'emoji' => '😐', 'color' => '#FFC107'],
    'sedih' => ['label' => 'Sedih', 'emoji' => '😞', 'color' => '#2196F3'],
    'sangat_sedih' => ['label' => 'Sangat Sedih', 'emoji' => '😢', 'color' => '#3F51B5'],
    'marah' => ['label' => 'Marah', 'emoji' => '😠', 'color' => '#F44336'],
    'cemas' => ['label' => 'Cemas', 'emoji' => '😰', 'color' => '#9C27B0']
];

// Generate chart data
$chart_data = [];
$mood_keys = array_keys($mood_options);
for ($i = 1; $i <= $days_in_month; $i++) {
    $date = date('Y-m-d', strtotime("$year-$month-$i"));
    $entry = array_filter($mood_entries, fn($e) => $e['date'] === $date);
    $chart_data[] = $entry ? array_search(reset($entry)['mood'], $mood_keys) : null;
}

$page_title = "Pelacak Mood";
require_once '../includes/header.php';
?>

<style>
  /* Global Styles */
:root {
    --primary-color: #6C63FF;
    --gray-color: #666;
    --light-gray: #f3f3f3;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
    color: #333;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Tracker Section */
.mood-tracker {
    padding: 40px 0;
}

.tracker-header {
    text-align: center;
    margin-bottom: 30px;
}

.tracker-header h1 {
    margin: 0;
    font-size: 2rem;
    color: var(--primary-color);
}

.month-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.month-selector .arrow {
    font-size: 1.5rem;
    color: var(--primary-color);
    text-decoration: none;
    transition: 0.2s;
}

.month-selector .arrow:hover {
    opacity: 0.8;
}

/* Grid Layout */
.tracker-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

/* Calendar Styles */
.calendar-container,
.mood-form-container,
.mood-chart-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.calendar-header {
    text-align: center;
    padding: 10px;
    background: var(--light-gray);
    border-radius: 5px;
    font-weight: 500;
}

.calendar-day {
    aspect-ratio: 1;
    padding: 10px;
    border: 1px solid #eee;
    position: relative;
    background-color: #fff;
    border-radius: 5px;
    transition: background-color 0.2s;
}

.calendar-day.today {
    background-color: #fff3e0;
}

.day-number {
    position: absolute;
    top: 5px;
    left: 5px;
    font-size: 0.8rem;
    color: var(--gray-color);
    font-weight: bold;
}

.mood-indicator {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    bottom: 5px;
    right: 5px;
    cursor: pointer;
    transition: transform 0.2s;
    font-size: 1.2rem;
    color: #fff;
}

.mood-indicator:hover {
    transform: scale(1.1);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="date"],
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

textarea {
    resize: vertical;
}

.mood-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.mood-option {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 2px solid #eee;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.mood-option:hover {
    border-color: var(--primary-color);
}

.mood-option input {
    display: none;
}

.mood-option .mood-emoji {
    font-size: 1.5rem;
    margin-right: 10px;
    transition: transform 0.2s;
}

.mood-option input:checked + .mood-emoji {
    transform: scale(1.2);
}

.btn.btn-primary {
    padding: 10px 20px;
    background-color: var(--primary-color);
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn.btn-primary:hover {
    background-color: #5a54e3;
}

/* Chart Section */
.mood-chart-container {
    margin-top: 30px;
}

/* Alerts */
.alert {
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
}

.alert-success {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.alert-danger {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

/* Responsive */
@media (max-width: 768px) {
    .tracker-grid {
        grid-template-columns: 1fr;
    }
}

</style>

<section class="mood-tracker">
    <div class="container">
        <div class="tracker-header">
            <h1>Pelacak Mood Harian</h1>
            <div class="month-selector">
                <a href="?month=<?= $month - 1 ?>&year=<?= $year ?>" class="arrow">&lt;</a>
                <h2><?= date('F Y', strtotime("$year-$month-01")) ?></h2>
                <a href="?month=<?= $month + 1 ?>&year=<?= $year ?>" class="arrow">&gt;</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="tracker-grid">
            <div class="calendar-container">
                <div class="calendar">
                    <?php foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day): ?>
                        <div class="calendar-header"><?= $day ?></div>
                    <?php endforeach; ?>

                    <?php foreach ($calendar as $day): ?>
                        <div class="calendar-day <?= isset($day['date']) && $day['date'] === $current_date ? 'today' : '' ?>">
                            <?php if ($day['day']): ?>
                                <div class="day-number"><?= $day['day'] ?></div>
                                <?php if ($day['mood']): ?>
                                    <div class="mood-indicator"
                                         style="background-color: <?= $mood_options[$day['mood']]['color'] ?>"
                                         data-date="<?= $day['date'] ?>"
                                         data-mood="<?= $day['mood'] ?>"
                                         data-notes="<?= htmlspecialchars($day['notes']) ?>">
                                        <?= $mood_options[$day['mood']]['emoji'] ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mood-form-container">
                <form id="moodForm" method="POST">
                    <div class="form-group">
                        <label for="selectedDate">Tanggal</label>
                        <input type="date" name="date" id="selectedDate" value="<?= $current_date ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Mood Hari Ini</label>
                        <div class="mood-options">
                            <?php foreach ($mood_options as $key => $option): ?>
                                <label class="mood-option">
                                    <input type="radio" name="mood" value="<?= $key ?>" required>
                                    <div class="mood-emoji"><?= $option['emoji'] ?></div>
                                    <span><?= $option['label'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea name="notes" id="notes" rows="4" placeholder="Bagaimana perasaanmu hari ini?"></textarea>
                    </div> -->

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

          <!-- Grafik -->
          <div class="mood-chart-container">
            <h3>Grafik Mood Bulan Ini</h3>
            <canvas id="moodChart"></canvas>
        </div>
    </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.mood-indicator').forEach(indicator => {
        indicator.addEventListener('click', function () {
            const date = this.dataset.date;
            const mood = this.dataset.mood;
            const notes = this.dataset.notes;

            document.getElementById('selectedDate').value = date;
            const moodInput = document.querySelector(`input[name="mood"][value="${mood}"]`);
            if (moodInput) moodInput.checked = true;
            document.getElementById('notes').value = notes || '';
        });
    });

    const ctx = document.getElementById('moodChart');
    if (ctx) {
        const moodOptions = <?= json_encode($mood_options) ?>;
        const chartData = <?= json_encode($chart_data) ?>;
        const daysInMonth = <?= $days_in_month ?>;
        const labels = Array.from({length: daysInMonth}, (_, i) => i + 1);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tren Mood Bulan Ini',
                    data: chartData,
                    borderColor: '#6C63FF',
                    tension: 0.4,
                    pointRadius: ctx => chartData[ctx.dataIndex] !== null ? 5 : 0,
                    pointBackgroundColor: ctx => {
                        const idx = chartData[ctx.dataIndex];
                        return idx !== null ? Object.values(moodOptions)[idx]?.color : 'transparent';
                    }
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            callback: value => Object.values(moodOptions)[value]?.label || ''
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const mood = Object.values(moodOptions)[ctx.raw];
                                return mood ? `${mood.label}` : 'Tidak ada data';
                            }
                        }
                    }
                }
            }
        });
    }
});

</script>

<style>
/* CSS tetap sama seperti kode kamu */
</style>

<?php require_once '../includes/footer.php'; ?>
