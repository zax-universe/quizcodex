<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'QuizCode — Challenge Your Mind' ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

<!-- Matrix Rain Canvas -->
<canvas id="matrixCanvas"></canvas>

<!-- Navbar -->
<nav class="navbar">
  <div class="nav-inner">
    <a href="/index.php" class="nav-brand">
      <span class="brand-icon"><i class="fas fa-terminal"></i></span>
      <span class="brand-text">Quiz<span class="accent">Code</span></span>
    </a>
    <div class="nav-links">
      <a href="/index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
      <a href="/pages/leaderboard.php" class="nav-link"><i class="fas fa-trophy"></i> Leaderboard</a>
      <a href="/pages/chat.php" class="nav-link" style="color:var(--accent2)"><i class="fas fa-robot"></i> Dabi AI</a>
      <?php if ($user): ?>
        <a href="/pages/quiz.php" class="nav-link btn-play"><i class="fas fa-play"></i> Main Quiz</a>
        <div class="nav-user">
          <a href="/pages/profile.php" class="user-avatar-btn">
            <?php if (!empty($user['avatar'])): ?>
              <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" class="avatar-xs">
            <?php else: ?>
              <span class="avatar-xs avatar-placeholder"><?= strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) ?></span>
            <?php endif; ?>
            <span><?= htmlspecialchars($user['username']) ?></span>
          </a>
          <a href="/api/logout.php" class="nav-link logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
      <?php else: ?>
        <a href="/pages/login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="/pages/register.php" class="nav-link btn-register"><i class="fas fa-user-plus"></i> Register</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <a href="/index.php"><i class="fas fa-home"></i> Home</a>
    <a href="/pages/leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
    <a href="/pages/chat.php" style="color:var(--accent2)"><i class="fas fa-robot"></i> Dabi AI</a>
    <?php if ($user): ?>
      <a href="/pages/quiz.php"><i class="fas fa-play"></i> Main Quiz</a>
      <a href="/pages/profile.php"><i class="fas fa-user"></i> Profile</a>
      <a href="/api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="/pages/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
      <a href="/pages/register.php"><i class="fas fa-user-plus"></i> Register</a>
    <?php endif; ?>
  </div>
</nav>
