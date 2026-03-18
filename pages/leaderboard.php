<?php
$pageTitle = 'Leaderboard — QuizCode';
require_once __DIR__ . '/../includes/header.php';
$lb = DB::getLeaderboard();
$scores = DB::read('scores');
?>

<div class="page-wrap">
  <div style="margin-bottom:2rem">
    <div class="section-title"><i class="fas fa-trophy"></i> Global Leaderboard <span class="tag">TOP 10</span></div>
    <p style="color:var(--text2);font-family:var(--font-mono);font-size:.82rem">// Pemain terbaik berdasarkan total skor kumulatif</p>
  </div>

  <!-- Top 3 Podium -->
  <?php if (count($lb) >= 1): ?>
  <div style="display:flex;justify-content:center;align-items:flex-end;gap:1.5rem;margin-bottom:3rem;flex-wrap:wrap">
    <?php
    $podium = [1=>null, 0=>null, 2=>null]; // order: 2nd, 1st, 3rd
    foreach ([1,0,2] as $pos):
      $p = $lb[$pos] ?? null;
      if (!$p) continue;
      $heights = [0=>'180px',1=>'140px',2=>'110px'];
      $colors  = [0=>'var(--accent4)',1=>'var(--text2)',2=>'#cd7f32'];
      $medals  = [0=>'🥇',1=>'🥈',2=>'🥉'];
      $pct = $p['total_quiz']>0 ? round(($p['total_score']/($p['total_quiz']*10))*100) : 0;
    ?>
    <div style="text-align:center;display:flex;flex-direction:column;align-items:center">
      <!-- Avatar -->
      <?php if (!empty($p['avatar'])): ?>
        <img src="/uploads/avatars/<?= htmlspecialchars($p['avatar']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid <?= $colors[$pos] ?>;margin-bottom:.5rem">
      <?php else: ?>
        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,<?= $colors[$pos] ?>,#333);display:flex;align-items:center;justify-content:center;font-family:var(--font-hd);font-size:1.4rem;font-weight:900;color:#000;border:3px solid <?= $colors[$pos] ?>;margin-bottom:.5rem">
          <?= strtoupper(substr($p['name']??$p['username'],0,1)) ?>
        </div>
      <?php endif; ?>
      <div style="font-size:1.5rem;margin-bottom:.3rem"><?= $medals[$pos] ?></div>
      <div style="font-family:var(--font-mono);font-size:.85rem;color:var(--text);margin-bottom:.2rem"><?= htmlspecialchars($p['username']) ?></div>
      <div style="font-family:var(--font-hd);font-size:1.3rem;color:<?= $colors[$pos] ?>;font-weight:900"><?= $p['total_score'] ?></div>
      <!-- Pillar -->
      <div style="width:90px;height:<?= $heights[$pos] ?>;background:var(--bg2);border:1px solid <?= $colors[$pos] ?>;border-bottom:none;border-radius:8px 8px 0 0;margin-top:.5rem;display:flex;align-items:center;justify-content:center;font-family:var(--font-hd);font-size:2rem;font-weight:900;color:<?= $colors[$pos] ?>">
        <?= $pos+1 ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Full Table -->
  <div class="terminal-card">
    <div class="terminal-bar">
      <span class="t-dot red"></span><span class="t-dot yel"></span><span class="t-dot grn"></span>
      <span class="terminal-title">leaderboard.json — <?= count($lb) ?> players</span>
    </div>
    <div style="overflow-x:auto">
      <table class="leaderboard-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Player</th>
            <th>User ID</th>
            <th>Quiz Dimainkan</th>
            <th>Total Skor</th>
            <th>Rata-rata</th>
            <th>Bergabung</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lb)): ?>
          <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text3);font-family:var(--font-mono)">
            // Belum ada pemain. Jadilah yang pertama!<br><br>
            <a href="/pages/register.php" class="btn btn-primary" style="display:inline-flex">Daftar Sekarang</a>
          </td></tr>
          <?php else: ?>
          <?php foreach ($lb as $i => $p):
            $avg = $p['total_quiz'] > 0 ? round(($p['total_score'] / ($p['total_quiz'] * 10)) * 100) : 0;
            $rankClass = $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-other'));
            $isCurrent = $user && $user['id'] === $p['id'];
          ?>
          <tr style="<?= $isCurrent ? 'background:rgba(0,255,136,.04)' : '' ?>">
            <td>
              <span class="rank-badge <?= $rankClass ?>"><?= $i===0?'🥇':($i===1?'🥈':($i===2?'🥉':$i+1)) ?></span>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:.6rem">
                <?php if (!empty($p['avatar'])): ?>
                  <img src="/uploads/avatars/<?= htmlspecialchars($p['avatar']) ?>" class="avatar-xs">
                <?php else: ?>
                  <span class="avatar-xs avatar-placeholder"><?= strtoupper(substr($p['name']??$p['username'],0,1)) ?></span>
                <?php endif; ?>
                <div>
                  <div style="color:var(--text);font-weight:600"><?= htmlspecialchars($p['username']) ?></div>
                  <?php if ($p['name'] && $p['name'] !== $p['username']): ?>
                  <div style="font-size:.72rem;color:var(--text3)"><?= htmlspecialchars($p['name']) ?></div>
                  <?php endif; ?>
                  <?php if ($isCurrent): ?><span class="badge badge-green" style="font-size:.6rem">YOU</span><?php endif; ?>
                </div>
              </div>
            </td>
            <td style="color:var(--accent);font-size:.78rem"><?= htmlspecialchars($p['id']) ?></td>
            <td style="color:var(--text2)"><?= $p['total_quiz'] ?>x</td>
            <td style="color:var(--accent);font-weight:700;font-size:1rem"><?= $p['total_score'] ?></td>
            <td>
              <span class="score-pill <?= $avg>=80?'score-great':($avg>=60?'score-good':($avg>=40?'score-ok':'score-low')) ?>"><?= $avg ?>%</span>
            </td>
            <td style="color:var(--text3);font-size:.78rem"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- CTA -->
  <?php if (!$user): ?>
  <div style="text-align:center;margin-top:2rem">
    <p style="color:var(--text2);font-family:var(--font-mono);font-size:.85rem;margin-bottom:1rem">// Login untuk bersaing di leaderboard</p>
    <div style="display:flex;gap:1rem;justify-content:center">
      <a href="/pages/register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Daftar</a>
      <a href="/pages/login.php" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Login</a>
    </div>
  </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
