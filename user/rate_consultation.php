<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

require_user();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$consultation_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT c.*, u.username AS counselor_name 
                        FROM consultations c
                        JOIN counselors co ON c.counselor_id = co.counselor_id
                        JOIN users u ON co.user_id = u.user_id
                        WHERE c.consultation_id = ? AND c.user_id = ?");
$stmt->bind_param("ii", $consultation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data tidak ditemukan.");
}

$consultation = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Rating tidak valid.";
    } else {
        $stmt = $conn->prepare("UPDATE consultations SET rating = ?, review = ? WHERE consultation_id = ?");
        $stmt->bind_param("isi", $rating, $review, $consultation_id);
        $stmt->execute();

        $stmt = $conn->prepare("
            UPDATE counselors SET rating = (
                SELECT AVG(rating) FROM consultations 
                WHERE counselor_id = ? AND rating IS NOT NULL
            )
            WHERE counselor_id = ?
        ");
        $stmt->bind_param("ii", $consultation['counselor_id'], $consultation['counselor_id']);
        $stmt->execute();

        $_SESSION['message'] = "Terima kasih atas penilaiannya!";
        header("Location: dashboard.php");
        exit;
    }
}

$page_title = "Beri Rating Konselor";
require_once '../includes/header.php';
?>

<style>
.rate-section .container {
  max-width: 600px;
  margin: auto;
  background: #fff;
  padding: 30px;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
}
.rate-section h2 {
  text-align: center;
  color: #333;
  margin-bottom: 25px;
}
.rate-section label {
  display: block;
  margin: 15px 0 5px;
  font-weight: 600;
}
.rate-section textarea {
  width: 100%;
  min-height: 100px;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  resize: vertical;
}
.rate-section .star-rating {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 15px 0 20px;
}
.rate-section .star {
  font-size: 32px;
  color: #ccc;
  cursor: pointer;
  transition: transform 0.2s ease, color 0.3s ease;
}
.rate-section .star:hover,
.rate-section .star.hover,
.rate-section .star.selected {
  color: #ffc107;
  transform: scale(1.2);
}
.rate-section .btn-submit {
  background-color: #00b894;
  color: white;
  border: none;
  padding: 12px 25px;
  font-size: 16px;
  border-radius: 8px;
  cursor: pointer;
  display: block;
  margin: 20px auto 0;
  transition: background-color 0.3s ease;
}
.rate-section .btn-submit:hover {
  background-color: #019875;
}
</style>

<section class="rate-section">
  <div class="container">
    <h2>Beri Rating untuk <?= htmlspecialchars($consultation['counselor_name']) ?></h2>

    <?php if (!empty($_SESSION['error'])): ?>
      <div style="color:red; text-align:center;"><?= $_SESSION['error'] ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
      <label for="rating">Pilih Rating</label>
      <div class="star-rating" id="star-rating">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <span class="star" data-value="<?= $i ?>">&#9733;</span>
        <?php endfor; ?>
      </div>
      <input type="hidden" name="rating" id="rating" required>

      <label for="review">Tulis Ulasan (opsional)</label>
      <textarea name="review" id="review" placeholder="Bagikan pengalamanmu..."></textarea>

      <button type="submit" class="btn-submit">Kirim Rating</button>
    </form>
  </div>
</section>

<script>
const stars = document.querySelectorAll('.rate-section .star-rating .star');
const ratingInput = document.getElementById('rating');
let currentRating = 0;

stars.forEach(star => {
  star.addEventListener('mouseover', () => {
    const value = parseInt(star.getAttribute('data-value'));
    highlightStars(value);
  });

  star.addEventListener('mouseout', () => {
    highlightStars(currentRating);
  });

  star.addEventListener('click', () => {
    currentRating = parseInt(star.getAttribute('data-value'));
    ratingInput.value = currentRating;
    highlightStars(currentRating);
  });
});

function highlightStars(rating) {
  stars.forEach(star => {
    const value = parseInt(star.getAttribute('data-value'));
    star.classList.toggle('selected', value <= rating);
  });
}
</script>

<?php require_once '../includes/footer.php'; ?>
