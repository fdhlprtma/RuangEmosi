<?php
session_start();
include '../includes/config.php';

// Validasi counselor_id
if (!isset($_GET['counselor_id'])) {
  echo "Konselor tidak ditemukan.";
  exit;
}
$counselor_id = intval($_GET['counselor_id']);
$current_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// ===================== HANDLE SUBMISSIONS =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Balasan awal konselor terhadap ulasan (parent_reply_id kosong)
  if (isset($_POST['reply'], $_POST['consultation_id']) && empty($_POST['parent_reply_id'])) {
    $consultation_id = intval($_POST['consultation_id']);
    $reply = $conn->real_escape_string(trim($_POST['reply']));
    if ($reply !== '') {
      $conn->query("UPDATE consultations SET counselor_reply='$reply', reply_date=NOW() WHERE consultation_id=$consultation_id AND counselor_id=$counselor_id");
    }
  }
  // Balasan user ke konsultasi (parent_reply_id kosong)
  if (isset($_POST['user_reply'], $_POST['consultation_id']) && empty($_POST['parent_reply_id'])) {
    $consultation_id = intval($_POST['consultation_id']);
    $user_reply = $conn->real_escape_string(trim($_POST['user_reply']));
    if ($user_reply !== '') {
      $conn->query("INSERT INTO consultation_replies (consultation_id, user_id, message) VALUES ($consultation_id, $current_user_id, '$user_reply')");
    }
  }
  // Balasan konselor terhadap balasan user (parent_reply_id tidak kosong)
  if (isset($_POST['counselor_reply_to_user'], $_POST['consultation_id'], $_POST['parent_reply_id'])) {
    $consultation_id = intval($_POST['consultation_id']);
    $parent_reply_id = intval($_POST['parent_reply_id']);
    $c_reply = $conn->real_escape_string(trim($_POST['counselor_reply_to_user']));
    if ($c_reply !== '') {
      $conn->query("INSERT INTO consultation_replies (consultation_id, user_id, message, parent_reply_id) VALUES ($consultation_id, $current_user_id, '$c_reply', $parent_reply_id)");
    }
  }
  header("Location: review.php?counselor_id=$counselor_id");
  exit;
}

// ===================== FETCH DATA =====================
// 1. Info konselor
$cQ = "
    SELECT c.counselor_id,
           c.user_id AS counselor_user_id,
           c.specialization,
           c.photo,
           c.hourly_rate,
           u.full_name AS counselor_name
    FROM counselors c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.counselor_id = $counselor_id
";
$counselor = $conn->query($cQ)->fetch_assoc();
if (!$counselor) exit("Konselor tidak ditemukan.");

// 2. Ambil ulasan dengan alias reviewer_name
$rQ = "
    SELECT con.consultation_id,
           con.user_id AS reviewer_id,
           con.rating,
           con.review,
           con.counselor_reply,
           con.reply_date,
           u.full_name AS reviewer_name
    FROM consultations con
    JOIN users u ON con.user_id = u.user_id
    WHERE con.counselor_id = $counselor_id
      AND con.rating IS NOT NULL
      AND con.review <> ''
    ORDER BY con.consultation_id DESC
";
$res = $conn->query($rQ);
$reviews = [];
$ids = [];
while ($row = $res->fetch_assoc()) {
  $reviews[] = $row;
  $ids[] = $row['consultation_id'];
}

// 3. Fetch semua balasan percakapan
$messages = [];
if (!empty($ids)) {
  $idList = implode(',', $ids);
  $mq = "
        SELECT id,
               consultation_id,
               user_id,
               message,
               parent_reply_id,
               created_at
        FROM consultation_replies
        WHERE consultation_id IN ($idList)
        ORDER BY created_at ASC
    ";
  $mr = $conn->query($mq);
  while ($m = $mr->fetch_assoc()) {
    $cid = $m['consultation_id'];
    $pid = $m['parent_reply_id'] ?: 0;
    $messages[$cid][$pid][] = $m;
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Ulasan Konselor - RuangEmosi</title>
  <style>
    .review-page {
      font-family: 'Inter', sans-serif;
      background: #f9fafb;
      margin: 0;
      padding: 20px;
    }

    .review-page .container {
      max-width: 800px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, .05);
    }

    .review-page .back {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      color: #2563eb;
      font-weight: bold;
    }

    .review-page .counselor-info {
      text-align: center;
      margin-bottom: 30px;
    }

    .review-page .counselor-info img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
    }

    .review-page .review {
      border-bottom: 1px solid #e5e7eb;
      padding: 15px 0;
    }

    .review-page .rating {
      color: #f59e0b;
      font-weight: bold;
    }

    .review-page .user,
    .review-page .message strong {
      font-size: 14px;
      color: #2563eb;
      cursor: pointer;
    }

    .review-page .user {
      color: #6b7280;
    }

    .review-page .reply-box,
    .review-page .message {
      background: #f3f4f6;
      padding: 15px;
      border-radius: 8px;
      margin-top: 10px;
    }

    .review-page .message {
      margin-left: 20px;
    }

    .review-page .reply-form {
      margin-top: 10px;
    }

    .review-page .reply-form.hidden {
      display: none;
    }

    .review-page .reply-form textarea {
      width: 100%;
      height: 80px;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      padding: 8px;
    }

    .review-page .reply-form button {
      margin-top: 8px;
      background: #2563eb;
      color: #fff;
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .review-page .see-more {
      color: #2563eb;
      cursor: pointer;
      margin-top: 5px;
      display: inline-block;
    }

    .review-page .hidden {
      display: none;
    }
  </style>
<?php include '../includes/header.php'; ?>
</head>

<body>
  <div class="review-page">
    <div class="container">
      <a href="index.php" class="back">&larr; Kembali</a>
      <div class="counselor-info">
        <img src="../<?= htmlspecialchars($counselor['photo']) ?>" alt="Foto Konselor">
        <h2><?= htmlspecialchars($counselor['counselor_name']) ?></h2>
        <p><strong><?= htmlspecialchars($counselor['specialization']) ?></strong> - Rp <?= number_format($counselor['hourly_rate'], 0, ',', '.') ?>/jam</p>
      </div>
      <h3>Ulasan Pengguna</h3>
      <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $r): ?>
          <div class="review">
            <div class="rating"><?= str_repeat('⭐', $r['rating']) ?> (<?= $r['rating'] ?>/5)</div>
            <div class="user">Oleh: <?= htmlspecialchars($r['reviewer_name']) ?></div>

            <!-- Form balasan awal konselor terhadap ulasan -->
            <?php if ($current_user_id == $counselor['counselor_user_id'] && empty($r['counselor_reply'])): ?>
              <div class="reply-form hidden">
                <form method="post" action="review.php?counselor_id=<?= $counselor_id ?>">
                  <textarea name="reply" placeholder="Balas ulasan..." required></textarea>
                  <input type="hidden" name="consultation_id" value="<?= $r['consultation_id'] ?>">
                  <button type="submit">Kirim Balasan</button>
                </form>
              </div>
            <?php endif; ?>

            <p><?= htmlspecialchars($r['review']) ?></p>

            <!-- Tampilkan balasan konselor jika ada -->
            <?php if ($r['counselor_reply']): ?>
              <div class="reply-box">
                <strong><?= htmlspecialchars($counselor['counselor_name']) ?>:</strong>
                <p><?= htmlspecialchars($r['counselor_reply']) ?></p>
              </div>
              <?php if ($current_user_id === $r['reviewer_id']): ?>
                <div class="reply-form hidden">
                  <form method="post" action="review.php?counselor_id=<?= $counselor_id ?>">
                    <textarea name="user_reply" placeholder="Balas ke konselor..." required></textarea>
                    <input type="hidden" name="consultation_id" value="<?= $r['consultation_id'] ?>">
                    <input type="hidden" name="parent_reply_id" value="0">
                    <button type="submit">Kirim Balasan</button>
                  </form>
                </div>
              <?php endif; ?>
            <?php endif; ?>


            <!-- Percakapan tambahan -->
            <?php $msgs = $messages[$r['consultation_id']] ?? []; ?>
            <?php if (isset($msgs[0])): ?>
              <?php $total = count($msgs[0]); ?>
              <?php foreach ($msgs[0] as $i => $msg): ?>
                <div class="message <?= $i >= 1 ? 'hidden' : '' ?>">
                  <strong><?= $msg['user_id'] == $current_user_id ? 'Anda' : 'User' ?>:</strong>
                  <p><?= htmlspecialchars($msg['message']) ?></p>
                </div>

                <!-- Balasan konselor terhadap pesan user -->
                <?php if (isset($msgs[$msg['id']])): ?>
                  <?php foreach ($msgs[$msg['id']] as $child): ?>
                    <div class="message message">
                      <strong><?= htmlspecialchars($counselor['counselor_name']) ?>:</strong>
                      <p><?= htmlspecialchars($child['message']) ?></p>
                    </div>
                  <?php endforeach; ?>
                <?php elseif ($current_user_id == $counselor['counselor_user_id']): ?>
                  <div class="reply-form hidden message">
                    <form method="post" action="review.php?counselor_id=<?= $counselor_id ?>">
                      <textarea name="counselor_reply_to_user" placeholder="Balas pesan..." required></textarea>
                      <input type="hidden" name="consultation_id" value="<?= $r['consultation_id'] ?>">
                      <input type="hidden" name="parent_reply_id" value="<?= $msg['id'] ?>">
                      <button type="submit">Kirim Balasan</button>
                    </form>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php if ($total > 3): ?>
                <span class="see-more">Lihat <?= $total - 1 ?> balasan</span>
              <?php endif; ?>
            <?php endif; ?>

            <!-- Form balasan user ke konselor -->
            <?php if ($current_user_id == $r['reviewer_id']): ?>
              <div class="reply-form hidden">
                <form method="post" action="review.php?counselor_id=<?= $counselor_id ?>">
                  <textarea name="user_reply" placeholder="Balas ke konselor..." required></textarea>
                  <input type="hidden" name="consultation_id" value="<?= $r['consultation_id'] ?>">
                  <input type="hidden" name="parent_reply_id" value="0">
                  <button type="submit">Kirim Balasan</button>
                </form>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="user">Belum ada ulasan untuk konselor ini.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Toggle reply forms on nama reviewer click (untuk balasan awal konselor)
    document.querySelectorAll('.review-page .user').forEach(el => {
      el.addEventListener('click', () => {
        const form = el.parentElement.querySelector('.reply-form');
        if (form) {
          form.classList.toggle('hidden');
          form.querySelector('textarea')?.focus();
        }
      });
    });
    // Toggle reply forms on pesan click (untuk balasan session)
    document.querySelectorAll('.review-page .message').forEach(el => {
      const form = el.nextElementSibling;
      if (form?.classList.contains('reply-form')) {
        el.addEventListener('click', () => {
          form.classList.toggle('hidden');
          form.querySelector('textarea')?.focus();
        });
      }
    });
    // See more replies
    document.querySelectorAll('.review-page .see-more').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.parentElement.querySelectorAll('.hidden').forEach(el => el.classList.remove('hidden'));
        btn.remove();
      });
    });

    // klik pada <strong> di .reply-box
document.querySelectorAll('.review-page .reply-box strong').forEach(el => {
  el.addEventListener('click', () => {
    const form = el.parentElement.nextElementSibling;
    if (form && form.classList.contains('reply-form')) {
      form.classList.toggle('hidden');
      form.querySelector('textarea')?.focus();
    }
  });
});

  </script>

  <?php include '../includes/footer.php'; ?>