<?php
$pageTitle = 'Profil — QuizCode';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$scores = array_values(DB::getUserScores($user['id']));
usort($scores, fn($a,$b) => strtotime($b['played_at']) - strtotime($a['played_at']));
$totalQuiz = count($scores);
$avgScore  = $totalQuiz > 0 ? round(array_sum(array_column($scores, 'percentage')) / $totalQuiz) : 0;
$bestScore = $totalQuiz > 0 ? max(array_column($scores, 'score')) : 0;

// Handle edit form
$editMsg = '';
$editErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $name  = trim($_POST['name'] ?? '');
    $bio   = trim($_POST['bio'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $newEmail    = trim($_POST['email'] ?? '');

    $update = [
        'name'  => $name ?: $user['username'],
        'bio'   => $bio,
        'email' => $newEmail ?: $user['email'],
    ];

    // Change username
    if ($newUsername && $newUsername !== $user['username']) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
            $editErr = 'Username hanya huruf, angka, underscore.';
        } elseif (DB::findUser($newUsername)) {
            $editErr = 'Username sudah dipakai.';
        } else {
            $update['username'] = $newUsername;
        }
    }

    // Change password
    if (!empty($_POST['new_password'])) {
        if (!password_verify($_POST['old_password'] ?? '', $user['password'])) {
            $editErr = 'Password lama salah.';
        } elseif (strlen($_POST['new_password']) < 6) {
            $editErr = 'Password baru minimal 6 karakter.';
        } else {
            $update['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }
    }

    // Avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $ext  = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $editErr = 'Format foto tidak didukung.';
        } elseif ($_FILES['avatar']['size'] > 2*1024*1024) {
            $editErr = 'Foto maksimal 2MB.';
        } else {
            $filename = $user['id'] . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/avatars/' . $filename;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                // Delete old
                if (!empty($user['avatar'])) {
                    @unlink(__DIR__ . '/../uploads/avatars/' . $user['avatar']);
                }
                $update['avatar'] = $filename;
            }
        }
    }

    if (!$editErr) {
        $user = DB::updateUser($user['id'], $update);
        $editMsg = 'Profil berhasil diperbarui!';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap">

  <!-- Profile Header -->
  <div class="profile-header">
    <?php if (!empty($user['avatar'])): ?>
      <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" class="avatar-lg">
    <?php else: ?>
      <div class="avatar-lg-placeholder"><?= strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) ?></div>
    <?php endif; ?>

    <div style="flex:1">
      <h2><?= htmlspecialchars($user['name'] ?? $user['username']) ?></h2>
      <div class="profile-id"><i class="fas fa-id-badge"></i> <?= htmlspecialchars($user['id']) ?> &nbsp;|&nbsp; @<?= htmlspecialchars($user['username']) ?></div>
      <div class="profile-bio"><?= htmlspecialchars($user['bio'] ?: 'Belum ada bio. Edit profil untuk menambahkan.') ?></div>
      <div class="profile-badges">
        <span class="badge badge-green"><i class="fas fa-check"></i> Verified</span>
        <?php if ($totalQuiz >= 10): ?><span class="badge badge-blue"><i class="fas fa-fire"></i> Active</span><?php endif; ?>
        <?php if ($avgScore >= 80): ?><span class="badge badge-red"><i class="fas fa-star"></i> Pro</span><?php endif; ?>
        <span class="badge badge-green"><i class="fas fa-calendar"></i> Bergabung <?= date('M Y', strtotime($user['created_at'])) ?></span>
      </div>
    </div>

    <div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.8rem;text-align:center;margin-bottom:1rem">
        <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.7rem">
          <div style="font-family:var(--font-hd);font-size:1.2rem;color:var(--accent)"><?= $totalQuiz ?></div>
          <div style="font-family:var(--font-mono);font-size:.65rem;color:var(--text3)">Quiz</div>
        </div>
        <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.7rem">
          <div style="font-family:var(--font-hd);font-size:1.2rem;color:var(--accent2)"><?= $avgScore ?>%</div>
          <div style="font-family:var(--font-mono);font-size:.65rem;color:var(--text3)">Rata-rata</div>
        </div>
        <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.7rem">
          <div style="font-family:var(--font-hd);font-size:1.2rem;color:var(--accent4)"><?= $bestScore ?></div>
          <div style="font-family:var(--font-mono);font-size:.65rem;color:var(--text3)">Best</div>
        </div>
      </div>
      <a href="/pages/quiz.php" class="btn btn-primary w-full" style="justify-content:center"><i class="fas fa-play"></i> Main Sekarang</a>
    </div>
  </div>

  <!-- Tabs -->
  <div class="profile-tabs">
    <button class="tab-btn active" data-tab="tabHistory"><i class="fas fa-history"></i> Riwayat Quiz</button>
    <button class="tab-btn" data-tab="tabEdit"><i class="fas fa-edit"></i> Edit Profil</button>
    <button class="tab-btn" data-tab="tabStats"><i class="fas fa-chart-bar"></i> Statistik</button>
  </div>

  <!-- Tab: History -->
  <div class="tab-panel active" id="tabHistory">
    <div class="terminal-card">
      <div class="terminal-bar">
        <span class="t-dot red"></span><span class="t-dot yel"></span><span class="t-dot grn"></span>
        <span class="terminal-title">quiz_history.json — <?= $totalQuiz ?> records</span>
      </div>
      <div style="overflow-x:auto">
        <table class="history-table">
          <thead>
            <tr>
              <th>#</th><th>Tanggal</th><th>Kategori</th><th>Skor</th><th>Benar</th><th>Grade</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($scores)): ?>
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text3);font-family:var(--font-mono)">
              // Belum ada riwayat quiz. <a href="/pages/quiz.php" style="color:var(--accent)">Mulai sekarang!</a>
            </td></tr>
            <?php else: ?>
            <?php foreach ($scores as $i => $s):
              $pct = $s['percentage'];
              $grade = $pct>=90?'A+':($pct>=80?'A':($pct>=70?'B':($pct>=60?'C':($pct>=50?'D':'F'))));
              $cls   = $pct>=80?'score-great':($pct>=60?'score-good':($pct>=40?'score-ok':'score-low'));
            ?>
            <tr>
              <td style="color:var(--text3)"><?= $i+1 ?></td>
              <td><?= date('d M Y H:i', strtotime($s['played_at'])) ?></td>
              <td><span class="badge badge-blue"><?= htmlspecialchars($s['category']) ?></span></td>
              <td><span class="score-pill <?= $cls ?>"><?= $pct ?>%</span></td>
              <td style="color:var(--text2)"><?= $s['score'] ?>/<?= $s['total'] ?></td>
              <td style="font-family:var(--font-hd);font-weight:700;color:<?= $pct>=70?'var(--accent)':($pct>=50?'var(--accent4)':'var(--accent3)') ?>"><?= $grade ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab: Edit -->
  <div class="tab-panel" id="tabEdit">
    <div class="terminal-card">
      <div class="terminal-bar">
        <span class="t-dot red"></span><span class="t-dot yel"></span><span class="t-dot grn"></span>
        <span class="terminal-title">edit_profile.php</span>
      </div>
      <div class="terminal-body">

        <?php if ($editMsg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($editMsg) ?></div><?php endif; ?>
        <?php if ($editErr): ?><div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($editErr) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="edit">

          <!-- Avatar -->
          <div class="edit-avatar-area">
            <?php if (!empty($user['avatar'])): ?>
              <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" class="avatar-lg" id="avatarPreview">
            <?php else: ?>
              <div class="avatar-lg-placeholder" id="avatarPreviewPlaceholder"><?= strtoupper(substr($user['name']??$user['username'],0,1)) ?></div>
              <img src="" class="avatar-lg" id="avatarPreview" style="display:none">
            <?php endif; ?>
            <div>
              <label class="avatar-upload-btn" for="avatarInput">
                <i class="fas fa-camera"></i> Ganti Foto Profil
              </label>
              <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none">
              <div class="form-hint" style="margin-top:.4rem">JPG, PNG, GIF, WebP — Max 2MB</div>
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-user"></i> Nama Tampil</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Nama lengkap">
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-at"></i> Username</label>
              <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" placeholder="username">
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" placeholder="email@example.com">
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-id-card"></i> User ID</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($user['id']) ?>" disabled style="color:var(--accent);cursor:not-allowed">
              <div class="form-hint">ID tidak bisa diubah</div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fas fa-align-left"></i> Bio</label>
            <textarea name="bio" class="form-control" rows="3" placeholder="Ceritakan tentang dirimu..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
          </div>

          <hr style="border:none;border-top:1px solid var(--border);margin:1.5rem 0">
          <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--accent);margin-bottom:1rem">// Ganti Password (opsional)</div>

          <div class="grid-2">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-lock"></i> Password Lama</label>
              <input type="password" name="old_password" class="form-control" placeholder="Password saat ini">
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-key"></i> Password Baru</label>
              <input type="password" name="new_password" class="form-control" placeholder="Min. 6 karakter">
            </div>
          </div>

          <div style="display:flex;gap:1rem;margin-top:.5rem">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <a href="/api/logout.php" class="btn btn-danger" onclick="return confirm('Yakin mau logout?')"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tab: Stats -->
  <div class="tab-panel" id="tabStats">
    <div class="grid-2">
      <div class="terminal-card">
        <div class="terminal-bar">
          <span class="t-dot red"></span><span class="t-dot yel"></span><span class="t-dot grn"></span>
          <span class="terminal-title">stats.json</span>
        </div>
        <div class="terminal-body">
          <div style="display:flex;flex-direction:column;gap:.8rem">
            <?php
            $statRows = [
              ['Total Quiz','fa-gamepad',$totalQuiz,'var(--accent)'],
              ['Total Skor','fa-star',$user['total_score']??0,'var(--accent4)'],
              ['Rata-rata','fa-chart-line',$avgScore.'%','var(--accent2)'],
              ['Skor Terbaik','fa-trophy',$bestScore.'/10','var(--accent)'],
              ['User ID','fa-fingerprint',$user['id'],'var(--accent2)'],
              ['Bergabung','fa-calendar-alt',date('d M Y', strtotime($user['created_at'])),'var(--text2)'],
            ];
            foreach ($statRows as [$label,$icon,$val,$color]):
            ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem .8rem;background:var(--bg3);border-radius:var(--radius);border:1px solid var(--border)">
              <span style="font-family:var(--font-mono);font-size:.8rem;color:var(--text2)"><i class="fas <?= $icon ?>" style="width:16px;color:<?= $color ?>"></i> <?= $label ?></span>
              <span style="font-family:var(--font-mono);font-size:.85rem;font-weight:600;color:<?= $color ?>"><?= $val ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="terminal-card">
        <div class="terminal-bar">
          <span class="t-dot red"></span><span class="t-dot yel"></span><span class="t-dot grn"></span>
          <span class="terminal-title">grade_distribution.json</span>
        </div>
        <div class="terminal-body">
          <?php
          $grades = ['A+'=>0,'A'=>0,'B'=>0,'C'=>0,'D'=>0,'F'=>0];
          foreach ($scores as $s) {
            $p = $s['percentage'];
            if ($p>=90) $grades['A+']++;
            elseif ($p>=80) $grades['A']++;
            elseif ($p>=70) $grades['B']++;
            elseif ($p>=60) $grades['C']++;
            elseif ($p>=50) $grades['D']++;
            else $grades['F']++;
          }
          $gradeColors = ['A+'=>'var(--accent)','A'=>'var(--accent)','B'=>'var(--accent2)','C'=>'var(--accent4)','D'=>'var(--accent4)','F'=>'var(--accent3)'];
          ?>
          <div style="font-family:var(--font-mono);font-size:.72rem;color:var(--text3);margin-bottom:1rem">// Distribusi Grade</div>
          <?php foreach ($grades as $g => $cnt): ?>
          <div style="margin-bottom:.7rem">
            <div style="display:flex;justify-content:space-between;margin-bottom:.25rem">
              <span style="font-family:var(--font-mono);font-size:.78rem;color:<?= $gradeColors[$g] ?>;font-weight:700"><?= $g ?></span>
              <span style="font-family:var(--font-mono);font-size:.75rem;color:var(--text3)"><?= $cnt ?> quiz</span>
            </div>
            <div style="height:6px;background:var(--bg3);border-radius:3px;overflow:hidden">
              <div style="height:100%;width:<?= $totalQuiz>0?round(($cnt/$totalQuiz)*100):0 ?>%;background:<?= $gradeColors[$g] ?>;border-radius:3px;transition:.5s"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
// Avatar preview with placeholder handling
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
const placeholder = document.getElementById('avatarPreviewPlaceholder');
if (avatarInput) {
  avatarInput.addEventListener('change', () => {
    const file = avatarInput.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => {
        if (avatarPreview) { avatarPreview.src = e.target.result; avatarPreview.style.display = 'block'; }
        if (placeholder) placeholder.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
