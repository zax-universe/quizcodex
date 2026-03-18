<?php
$pageTitle = 'Login — QuizCode';
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $result = login($username, $password);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $redirect = $_GET['redirect'] ?? '/index.php';
            header('Location: ' . $redirect);
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
        <span class="terminal-title">auth/login.php</span>
      </div>
      <div class="terminal-body">
        <div style="margin-bottom:1.5rem">
          <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--accent);margin-bottom:.4rem">
            <i class="fas fa-terminal"></i> $ ./login --session
          </div>
          <h2 style="font-family:var(--font-hd);font-size:1.6rem">Selamat Datang Kembali</h2>
          <p style="color:var(--text2);font-size:.85rem;margin-top:.3rem">Masuk ke akun QuizCode kamu</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label class="form-label"><i class="fas fa-at"></i> Username</label>
            <input type="text" name="username" class="form-control" placeholder="username_kamu" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <div style="position:relative">
              <input type="password" name="password" id="passInput" class="form-control" placeholder="••••••••" required style="padding-right:3rem">
              <button type="button" onclick="togglePass()" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text2);cursor:pointer">
                <i class="fas fa-eye" id="passEye"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-full" style="margin-top:1rem;justify-content:center">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </form>

        <div class="auth-footer">
          Belum punya akun? <a href="/pages/register.php">Register sekarang</a>
        </div>

        <div class="auth-footer" style="margin-top:.5rem">
          <a href="/index.php" style="color:var(--text2)"><i class="fas fa-arrow-left"></i> Kembali ke Home</a>
        </div>
      </div>
    </div>

    <!-- Decorative code -->
    <div class="code-block" style="margin-top:1rem;font-size:.72rem">
      <span class="cm">// QuizCode Authentication Module</span><br>
      <span class="kw">if</span> (credentials.<span class="fn">valid</span>()) {<br>
      &nbsp;&nbsp;<span class="kw">return</span> session.<span class="fn">start</span>(user);<br>
      } <span class="kw">else</span> { <span class="fn">throw</span> <span class="str">"try again"</span>; }
    </div>
  </div>
</div>

<script>
function togglePass() {
  const inp = document.getElementById('passInput');
  const eye = document.getElementById('passEye');
  if (inp.type === 'password') { inp.type = 'text'; eye.className = 'fas fa-eye-slash'; }
  else { inp.type = 'password'; eye.className = 'fas fa-eye'; }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
