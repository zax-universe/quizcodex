<?php
$pageTitle = 'Quiz — QuizCode';
require_once __DIR__ . '/../includes/header.php';
$isGuest = !$user;
?>

<div class="page-wrap">
  <div class="quiz-wrap">

    <?php if ($isGuest): ?>
    <!-- Guest notice -->
    <div class="alert alert-error" style="margin-bottom:1.5rem">
      <i class="fas fa-lock"></i>
      Mode tamu — kamu bisa lihat tapi <strong>tidak bisa menyimpan skor</strong>.
      <a href="/pages/login.php" style="color:var(--accent);margin-left:.5rem">Login</a> atau
      <a href="/pages/register.php" style="color:var(--accent2);margin-left:.3rem">Register</a> untuk bermain penuh.
    </div>
    <?php endif; ?>

    <!-- QUIZ START -->
    <div id="quizStart">
      <div class="terminal-card">
        <div class="terminal-bar">
          <span class="t-dot red"></span>
          <span class="t-dot yel"></span>
          <span class="t-dot grn"></span>
          <span class="terminal-title">quiz_engine.php — ready</span>
        </div>
        <div class="terminal-body" style="text-align:center;padding:3rem 2rem">
          <div style="font-size:3rem;color:var(--accent);margin-bottom:1rem"><i class="fas fa-brain"></i></div>
          <h2 style="font-family:var(--font-hd);font-size:1.8rem;margin-bottom:.5rem">Siap Untuk Tantangan?</h2>
          <p style="color:var(--text2);margin-bottom:2rem;font-size:.9rem">10 soal pilihan ganda • Timer 20 detik/soal • Skor real-time</p>

          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;max-width:400px;margin:0 auto 2rem;text-align:center">
            <div style="padding:1rem;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius)">
              <div style="font-family:var(--font-hd);font-size:1.4rem;color:var(--accent)">10</div>
              <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Soal</div>
            </div>
            <div style="padding:1rem;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius)">
              <div style="font-family:var(--font-hd);font-size:1.4rem;color:var(--accent2)">20s</div>
              <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Timer</div>
            </div>
            <div style="padding:1rem;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius)">
              <div style="font-family:var(--font-hd);font-size:1.4rem;color:var(--accent4)">∞</div>
              <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Ulang</div>
            </div>
          </div>

          <?php if ($isGuest): ?>
            <p style="font-family:var(--font-mono);font-size:.78rem;color:var(--accent3);margin-bottom:1.2rem">
              <i class="fas fa-info-circle"></i> Login untuk menyimpan skor ke leaderboard
            </p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
              <button id="startQuizBtn" class="btn btn-outline"><i class="fas fa-play"></i> Main Tanpa Simpan</button>
              <a href="/pages/login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login Dulu</a>
            </div>
          <?php else: ?>
            <button id="startQuizBtn" class="btn btn-primary" style="font-size:1rem;padding:1rem 2.5rem">
              <i class="fas fa-play"></i> Mulai Quiz
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- QUIZ GAME -->
    <div id="quizGame" style="display:none">
      <div class="terminal-card">
        <div class="terminal-bar">
          <span class="t-dot red"></span>
          <span class="t-dot yel"></span>
          <span class="t-dot grn"></span>
          <span class="terminal-title" id="qCat">quiz_engine.php</span>
        </div>
        <div style="padding:1.5rem">
          <div class="quiz-header">
            <div class="quiz-meta">
              <span><i class="fas fa-question-circle" style="color:var(--accent)"></i> <span id="qNum">Soal 1/10</span></span>
              <span><i class="fas fa-star" style="color:var(--accent4)"></i> Skor: <span id="qScore" style="color:var(--accent4);font-weight:700">0</span></span>
            </div>
            <div class="timer-ring" id="timerRing">
              <span id="timerDisplay">20</span>
            </div>
          </div>
          <div class="quiz-progress"><div class="quiz-progress-bar" id="quizProgress" style="width:0%"></div></div>

          <div class="question-box">
            <div class="question-num" id="qNumLabel">// Question 01</div>
            <div class="question-text" id="qText">Loading soal...</div>
            <div class="options-grid" id="optionsContainer"></div>
          </div>

          <div class="feedback-msg" id="feedback"></div>

          <div class="quiz-nav">
            <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--text3)">
              <i class="fas fa-clock"></i> Sisa waktu: <span id="timerDisplay2"></span>
            </div>
            <button id="nextBtn" class="btn btn-primary" style="display:none">
              Lanjut <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- QUIZ RESULT -->
    <div id="quizResult" style="display:none">
      <div class="terminal-card">
        <div class="terminal-bar">
          <span class="t-dot red"></span>
          <span class="t-dot yel"></span>
          <span class="t-dot grn"></span>
          <span class="terminal-title">quiz_result.json</span>
        </div>
        <div class="terminal-body">
          <div class="result-box">
            <div style="font-family:var(--font-mono);font-size:.8rem;color:var(--text2);margin-bottom:.5rem">
              <i class="fas fa-flag-checkered"></i> Quiz Selesai!
            </div>
            <div class="result-score" id="finalScore">0%</div>
            <div style="font-family:var(--font-hd);font-size:2rem;margin-bottom:.3rem" id="finalGrade">F</div>
            <div class="result-label">Benar: <span id="finalCorrect">0/10</span></div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;max-width:360px;margin:1.5rem auto">
              <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem;text-align:center">
                <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Skor</div>
                <div style="color:var(--accent);font-family:var(--font-hd)" id="finalScoreNum">-</div>
              </div>
              <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem;text-align:center">
                <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Total</div>
                <div style="color:var(--accent2);font-family:var(--font-hd)">10</div>
              </div>
              <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:.8rem;text-align:center">
                <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3)">Grade</div>
                <div style="font-family:var(--font-hd)" id="finalGrade2">-</div>
              </div>
            </div>

            <?php if (!$isGuest): ?>
            <div class="alert alert-success" style="display:inline-flex;margin-bottom:1.5rem">
              <i class="fas fa-save"></i> Skor tersimpan ke profil & leaderboard!
            </div>
            <?php else: ?>
            <div class="alert alert-error" style="display:inline-flex;margin-bottom:1.5rem">
              <i class="fas fa-exclamation"></i> Skor tidak tersimpan. <a href="/pages/register.php" style="color:var(--accent)">Daftar</a> untuk menyimpan!
            </div>
            <?php endif; ?>

            <div class="result-actions">
              <button onclick="QuizEngine.playAgain()" class="btn btn-primary"><i class="fas fa-redo"></i> Main Lagi</button>
              <a href="/pages/leaderboard.php" class="btn btn-secondary"><i class="fas fa-trophy"></i> Leaderboard</a>
              <?php if (!$isGuest): ?>
              <a href="/pages/profile.php" class="btn btn-outline"><i class="fas fa-user"></i> Profil</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
// Override finish to also update result UI extras
const origFinish = QuizEngine.finish.bind(QuizEngine);
QuizEngine.finish = async function() {
  await origFinish();
  document.getElementById('finalScoreNum').textContent = this.score;
  document.getElementById('finalGrade2').textContent = document.getElementById('finalGrade').textContent;
  document.getElementById('finalGrade2').style.color = document.getElementById('finalGrade').style.color;
};
const isGuest = <?= $isGuest ? 'true' : 'false' ?>;
if (isGuest) {
  const origSave = QuizEngine.finish.bind(QuizEngine);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
