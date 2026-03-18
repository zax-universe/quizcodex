<?php
$pageTitle = 'QuizCode — Challenge Your Mind';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
$lb = DB::getLeaderboard();
$totalUsers = count(DB::read('users'));
$totalScores = count(DB::read('scores'));
?>

<div class="page-wrap">

  <!-- HERO -->
  <section class="hero">
    <div class="hero-tag">
      <i class="fas fa-circle" style="font-size:.5rem;animation:pulseGlow 1s infinite alternate"></i>
      LIVE — Quiz Pilihan Ganda Interaktif
    </div>
    <h1 class="glitch" data-text="QUIZ_CODE">
      <span class="accent">QUIZ</span>_CODE
    </h1>
    <p>Platform kuis berbasis terminal. Uji pengetahuanmu dengan soal-soal random, bersaing di leaderboard, dan buktikan kemampuanmu.</p>
    <div class="hero-btns">
      <?php if ($user): ?>
        <a href="/pages/quiz.php" class="btn btn-primary"><i class="fas fa-play"></i> Mulai Quiz</a>
        <a href="/pages/profile.php" class="btn btn-secondary"><i class="fas fa-user"></i> Profil Saya</a>
      <?php else: ?>
        <a href="/pages/register.php" class="btn btn-primary"><i class="fas fa-rocket"></i> Mulai Sekarang</a>
        <a href="/pages/quiz.php" class="btn btn-secondary"><i class="fas fa-eye"></i> Lihat Dulu</a>
      <?php endif; ?>
    </div>
  </section>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-users"></i></div>
      <div class="stat-num"><?= $totalUsers ?></div>
      <div class="stat-label">// Total Players</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
      <div class="stat-num"><?= $totalScores ?></div>
      <div class="stat-label">// Quiz Dimainkan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-infinity"></i></div>
      <div class="stat-num">∞</div>
      <div class="stat-label">// Soal Tersedia</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-bolt"></i></div>
      <div class="stat-num">20<span style="font-size:1.2rem">s</span></div>
      <div class="stat-label">// Per Soal</div>
    </div>
  </div>

  <!-- PREVIEW & LEADERBOARD ROW -->
  <div class="grid-2">

    <!-- HOW IT WORKS -->
    <div class="terminal-card">
      <div class="terminal-bar">
        <span class="t-dot red"></span>
        <span class="t-dot yel"></span>
        <span class="t-dot grn"></span>
        <span class="terminal-title">how_it_works.sh</span>
      </div>
      <div class="terminal-body">
        <div style="margin-bottom:1.2rem">
          <div style="color:var(--text3);font-size:.75rem;margin-bottom:.5rem">// Cara Bermain</div>
          <div style="display:flex;flex-direction:column;gap:.9rem">
            <?php
            $steps = [
              ['fa-user-plus','Register / Login','Buat akun gratis untuk menyimpan progres dan bersaing.'],
              ['fa-play-circle','Mulai Quiz','10 soal random dengan timer 20 detik per soal.'],
              ['fa-check-double','Jawab Soal','Pilih salah satu dari 4 pilihan jawaban.'],
              ['fa-trophy','Lihat Hasil','Cek skor, grade, dan posisi di leaderboard!'],
            ];
            foreach ($steps as $i => [$icon, $title, $desc]):
            ?>
            <div style="display:flex;gap:1rem;align-items:flex-start">
              <div style="width:34px;height:34px;border-radius:8px;background:var(--bg3);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--accent);flex-shrink:0">
                <i class="fas <?= $icon ?>"></i>
              </div>
              <div>
                <div style="font-family:var(--font-mono);font-size:.82rem;color:var(--text);margin-bottom:.2rem"><?= $title ?></div>
                <div style="font-size:.78rem;color:var(--text2)"><?= $desc ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="code-block" style="margin-top:1rem">
<span class="cm">// Start your journey</span>
<span class="kw">function</span> <span class="fn">startChallenge</span>() {
  <span class="kw">const</span> player = <span class="fn">login</span>();
  <span class="kw">while</span> (player.wantsToPlay) {
    <span class="kw">const</span> q = <span class="fn">fetchQuestion</span>();
    player.<span class="fn">answer</span>(q);
    player.score++;
  }
}
        </div>
      </div>
    </div>

    <!-- TOP 5 LEADERBOARD -->
    <div class="terminal-card">
      <div class="terminal-bar">
        <span class="t-dot red"></span>
        <span class="t-dot yel"></span>
        <span class="t-dot grn"></span>
        <span class="terminal-title">top_players.json</span>
      </div>
      <div class="terminal-body" style="padding:0">
        <table class="leaderboard-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Player</th>
              <th>Quiz</th>
              <th>Score</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($lb)): ?>
            <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text3);font-family:var(--font-mono);font-size:.8rem">
              // No players yet — be the first!
            </td></tr>
            <?php else: ?>
            <?php foreach (array_slice($lb, 0, 5) as $i => $p): ?>
            <tr>
              <td>
                <span class="rank-badge <?= $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-other')) ?>">
                  <?= $i===0?'🥇':($i===1?'🥈':($i===2?'🥉':$i+1)) ?>
                </span>
              </td>
              <td style="color:var(--text)">
                <?php if (!empty($p['avatar'])): ?>
                  <img src="/uploads/avatars/<?= htmlspecialchars($p['avatar']) ?>" class="avatar-xs" style="vertical-align:middle;margin-right:.4rem">
                <?php else: ?>
                  <span class="avatar-xs avatar-placeholder" style="vertical-align:middle;margin-right:.4rem;display:inline-flex"><?= strtoupper(substr($p['name']??$p['username'],0,1)) ?></span>
                <?php endif; ?>
                <?= htmlspecialchars($p['username']) ?>
              </td>
              <td style="color:var(--text2)"><?= $p['total_quiz'] ?>x</td>
              <td style="color:var(--accent);font-weight:700"><?= $p['total_score'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
        <div style="padding:1rem;text-align:center">
          <a href="/pages/leaderboard.php" class="btn btn-outline" style="font-size:.8rem;padding:.5rem 1.2rem">
            <i class="fas fa-list"></i> Lihat Semua
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- FEATURES -->
  <section style="margin-top:3rem">
    <div class="section-title"><i class="fas fa-star"></i> Fitur Unggulan <span class="tag">v1.0</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
      <?php
      $features = [
        ['fa-random','Soal Random','Ribuan soal berbagai kategori dari API real-time.','var(--accent)'],
        ['fa-stopwatch','Timer 20 Detik','Tantangan waktu bikin makin seru dan menegangkan.','var(--accent2)'],
        ['fa-chart-bar','Statistik Lengkap','Pantau perkembangan skor dan riwayat quiz kamu.','var(--accent4)'],
        ['fa-medal','Leaderboard','Bersaing dengan pemain lain, rebut posisi teratas.','var(--accent3)'],
        ['fa-user-cog','Profil Kustom','Edit nama, foto profil, bio, dan info lainnya.','var(--accent)'],
        ['fa-shield-alt','Aman & Privat','Data tersimpan aman, password di-hash bcrypt.','var(--accent2)'],
      ];
      foreach ($features as [$icon, $title, $desc, $color]):
      ?>
      <div class="stat-card" style="text-align:left">
        <div style="font-size:1.5rem;color:<?= $color ?>;margin-bottom:.7rem"><i class="fas <?= $icon ?>"></i></div>
        <div style="font-family:var(--font-mono);font-size:.88rem;color:var(--text);margin-bottom:.3rem"><?= $title ?></div>
        <div style="font-size:.8rem;color:var(--text2)"><?= $desc ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- CTA -->
  <?php if (!$user): ?>
  <section style="margin-top:3rem;text-align:center;padding:3rem 2rem;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius2);position:relative;overflow:hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--accent),var(--accent2),var(--accent3))"></div>
    <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--accent);margin-bottom:.8rem">// JOIN THE CHALLENGE</div>
    <h2 style="font-family:var(--font-hd);font-size:2rem;margin-bottom:.8rem">Siap Membuktikan Dirimu?</h2>
    <p style="color:var(--text2);margin-bottom:2rem;max-width:400px;margin-left:auto;margin-right:auto">Daftar sekarang, gratis selamanya. Mulai tantang kemampuanmu!</p>
    <div class="hero-btns">
      <a href="/pages/register.php" class="btn btn-primary"><i class="fas fa-rocket"></i> Daftar Gratis</a>
      <a href="/pages/login.php" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Login</a>
    </div>
  </section>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
