<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span><i class="fas fa-terminal"></i> Quiz<span class="accent">Code</span></span>
      <p>Challenge Your Mind, One Question at a Time</p>
    </div>
    <div class="footer-links">
      <a href="/index.php">Home</a>
      <a href="/pages/leaderboard.php">Leaderboard</a>
      <a href="/pages/quiz.php">Quiz</a>
      <a href="/pages/chat.php" style="color:var(--accent2)"><i class="fas fa-robot"></i> Dabi AI</a>
    </div>
    <div class="footer-credits">
      <span>API by <a href="https://ikyyzyyrestapi.my.id" target="_blank">IkyyOfficial</a></span>
      <span>Built with <i class="fas fa-heart" style="color:#ff4d6d"></i> & PHP</span>
    </div>
  </div>
  <div class="footer-bottom">
    <span>// &copy; <?= date('Y') ?> QuizCode. All rights reserved.</span>
  </div>
</footer>

<script src="/assets/js/main.js"></script>

<?php if (basename($_SERVER['PHP_SELF']) !== 'chat.php'): ?>
<a href="/pages/chat.php" class="float-ai-btn" title="Chat dengan Dabi AI">
  <i class="fas fa-robot"></i>
  <span class="float-ai-ping"></span>
</a>
<style>
.float-ai-btn{position:fixed;bottom:1.8rem;right:1.8rem;width:54px;height:54px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));color:#000;font-size:1.3rem;display:flex;align-items:center;justify-content:center;text-decoration:none;z-index:999;box-shadow:0 4px 20px rgba(0,255,136,.4);transition:.25s}
.float-ai-btn:hover{transform:scale(1.12);box-shadow:0 6px 30px rgba(0,255,136,.6)}
.float-ai-ping{position:absolute;top:4px;right:4px;width:12px;height:12px;border-radius:50%;background:var(--accent3);border:2px solid var(--bg);animation:ping .9s infinite}
@keyframes ping{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}
</style>
<?php endif; ?>

</body>
</html>
