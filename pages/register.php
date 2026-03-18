<?php
$pageTitle = 'Register — QuizCode';
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        $result = DB::createUser($username, $email, $password);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            startSession();
            $_SESSION['user_id'] = $result['id'];
            header('Location: /index.php?welcome=1');
            exit;
        }
    }
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap">
  <div class="auth-wrap">
    <div class="terminal-card">
      <div class="terminal-bar">
        <span class="t-dot red"></span>
        <span class="t-dot yel"></span>
        <span class="t-dot grn"></span>
        <span class="terminal-title">auth/register.php</span>
      </div>
      <div class="terminal-body">
        <div style="margin-bottom:1.5rem">
          <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--accent);margin-bottom:.4rem">
            <i class="fas fa-terminal"></i> $ ./register --new-user
          </div>
          <h2 style="font-family:var(--font-hd);font-size:1.6rem">Buat Akun Baru</h2>
          <p style="color:var(--text2);font-size:.85rem;margin-top:.3rem">Bergabung dengan komunitas QuizCode</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="grid-2" style="gap:.8rem">
            <div class="form-group">
              <label class="form-label"><i class="fas fa-at"></i> Username</label>
              <input type="text" name="username" class="form-control" placeholder="cool_username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
              <div class="form-hint">Huruf, angka, underscore</div>
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
              <input type="email" name="email" class="form-control" placeholder="kamu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" id="passInput" class="form-control" placeholder="Min. 6 karakter" required>
            <div class="form-hint">Minimal 6 karakter</div>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Konfirmasi Password</label>
            <input type="password" name="confirm" id="confirmInput" class="form-control" placeholder="Ulangi password" required>
          </div>

          <!-- Strength indicator -->
          <div id="strengthBar" style="height:4px;background:var(--border);border-radius:2px;margin-bottom:1rem;overflow:hidden">
            <div id="strengthFill" style="height:100%;width:0;transition:.3s;border-radius:2px"></div>
          </div>
          <div id="strengthText" style="font-family:var(--font-mono);font-size:.7rem;color:var(--text3);margin-bottom:.5rem"></div>

          <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
            <input type="checkbox" id="agreeCheck" required style="accent-color:var(--accent)">
            <label for="agreeCheck" style="font-family:var(--font-mono);font-size:.78rem;color:var(--text2);cursor:pointer">
              Saya setuju dengan syarat dan ketentuan
            </label>
          </div>

          <button type="submit" class="btn btn-primary w-full" style="margin-top:.5rem;justify-content:center">
            <i class="fas fa-user-plus"></i> Buat Akun
          </button>
        </form>

        <div class="auth-footer">
          Sudah punya akun? <a href="/pages/login.php">Login di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const passInput = document.getElementById('passInput');
const fill = document.getElementById('strengthFill');
const text = document.getElementById('strengthText');
passInput.addEventListener('input', () => {
  const v = passInput.value;
  let score = 0;
  if (v.length >= 6) score++;
  if (v.length >= 10) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^a-zA-Z0-9]/.test(v)) score++;
  const levels = [
    [0,'',''],
    [20,'#ff4d6d','Sangat Lemah'],
    [40,'#f0c040','Lemah'],
    [60,'#0af','Cukup'],
    [80,'#00ff88','Kuat'],
    [100,'#00ff88','Sangat Kuat'],
  ];
  const [w, c, l] = levels[score] || levels[0];
  fill.style.width = w + '%';
  fill.style.background = c;
  text.textContent = l ? `// Kekuatan: ${l}` : '';
  text.style.color = c;
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
