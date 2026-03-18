<?php
$pageTitle = 'Dabi AI — QuizCode';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap" style="max-width:900px">

  <div style="margin-bottom:1.5rem">
    <div class="section-title"><i class="fas fa-robot"></i> Dabi AI Assistant <span class="tag">BETA</span></div>
    <p style="color:var(--text2);font-family:var(--font-mono);font-size:.8rem">// Tanya apapun — soal quiz, fitur platform, atau ngobrol santai aja</p>
  </div>

  <div class="terminal-card" style="height:calc(100vh - 280px);min-height:500px;display:flex;flex-direction:column">
    <div class="terminal-bar">
      <span class="t-dot red"></span>
      <span class="t-dot yel"></span>
      <span class="t-dot grn"></span>
      <span class="terminal-title">dabi_ai.php — online</span>
      <span style="margin-left:auto;display:flex;align-items:center;gap:.4rem;font-family:var(--font-mono);font-size:.7rem;color:var(--accent)">
        <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block;animation:blink 1.5s infinite"></span>
        Dabi is online
      </span>
    </div>

    <!-- CHAT MESSAGES -->
    <div id="chatMessages" style="flex:1;overflow-y:auto;padding:1.2rem;display:flex;flex-direction:column;gap:1rem">

      <!-- Welcome message -->
      <div class="msg-row msg-ai">
        <div class="msg-avatar ai-avatar"><i class="fas fa-robot"></i></div>
        <div class="msg-bubble ai-bubble">
          <div class="msg-name">Dabi <span class="msg-time"><?= date('H:i') ?></span></div>
          <div class="msg-text">Haii~ aku Dabi, AI assistant QuizCode 👋<br><br>
          Aku bisa bantu kamu soal:<br>
          • 🎮 Cara main &amp; tips quiz<br>
          • 📚 Pertanyaan pengetahuan umum<br>
          • ⚙️ Fitur &amp; setup QuizCode<br>
          • 💬 Atau ngobrol santai aja<br><br>
          Mau tanya apa nih? 😊</div>
        </div>
      </div>

      <!-- Suggested questions -->
      <div id="suggestions" style="display:flex;flex-wrap:wrap;gap:.5rem;padding-left:3rem">
        <?php
        $suggestions = [
          'Cara dapat nilai A+ di quiz?',
          'Fitur apa aja di QuizCode?',
          'Gimana cara install QuizCode?',
          'Tips belajar yang efektif?',
        ];
        foreach ($suggestions as $s):
        ?>
        <button class="suggest-btn" onclick="sendSuggestion('<?= htmlspecialchars($s) ?>')"><?= $s ?></button>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- TYPING INDICATOR -->
    <div id="typingIndicator" style="display:none;padding:.5rem 1.2rem;align-items:center;gap:.8rem">
      <div class="msg-avatar ai-avatar" style="width:28px;height:28px;font-size:.7rem"><i class="fas fa-robot"></i></div>
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:12px;padding:.5rem .9rem;display:flex;gap:.3rem;align-items:center">
        <span class="typing-dot"></span>
        <span class="typing-dot" style="animation-delay:.2s"></span>
        <span class="typing-dot" style="animation-delay:.4s"></span>
      </div>
    </div>

    <!-- INPUT AREA -->
    <div style="border-top:1px solid var(--border);padding:1rem;display:flex;gap:.7rem;align-items:flex-end">
      <textarea id="chatInput"
        placeholder="Ketik pesan ke Dabi... (Enter kirim, Shift+Enter baris baru)"
        rows="1"
        style="flex:1;background:var(--bg3);border:1px solid var(--border2);border-radius:10px;padding:.7rem 1rem;color:var(--text);font-family:var(--font-mono);font-size:.85rem;resize:none;outline:none;max-height:120px;overflow-y:auto;transition:.2s;line-height:1.5"
        onkeydown="handleKey(event)"
        oninput="autoResize(this)"
      ></textarea>
      <button id="sendBtn" onclick="sendMessage()" style="width:44px;height:44px;border-radius:10px;background:var(--accent);border:none;color:#000;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0">
        <i class="fas fa-paper-plane"></i>
      </button>
      <button onclick="resetChat()" title="Reset sesi chat" style="width:44px;height:44px;border-radius:10px;background:var(--bg3);border:1px solid var(--border2);color:var(--text2);font-size:.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0">
        <i class="fas fa-redo"></i>
      </button>
    </div>
  </div>

</div>

<style>
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.msg-row{display:flex;gap:.7rem;align-items:flex-start}
.msg-row.msg-user{flex-direction:row-reverse}
.msg-avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0}
.ai-avatar{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#000}
.user-avatar{background:linear-gradient(135deg,var(--accent2),#7c3aed);color:#fff}
.msg-bubble{max-width:75%;display:flex;flex-direction:column;gap:.3rem}
.msg-name{font-family:var(--font-mono);font-size:.68rem;color:var(--text3);display:flex;align-items:center;gap:.5rem}
.msg-time{color:var(--text3);font-size:.65rem}
.msg-text{padding:.75rem 1rem;border-radius:12px;font-size:.88rem;line-height:1.6}
.ai-bubble .msg-text{background:var(--bg3);border:1px solid var(--border);border-top-left-radius:4px;color:var(--text)}
.user-bubble .msg-text{background:linear-gradient(135deg,rgba(0,255,136,.15),rgba(0,170,255,.15));border:1px solid rgba(0,255,136,.3);border-top-right-radius:4px;color:var(--text)}
.msg-row.msg-user .msg-name{justify-content:flex-end}
.suggest-btn{font-family:var(--font-mono);font-size:.72rem;padding:.35rem .8rem;background:var(--bg3);border:1px solid var(--border2);border-radius:20px;color:var(--text2);cursor:pointer;transition:.2s}
.suggest-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--glow)}
.typing-dot{width:7px;height:7px;border-radius:50%;background:var(--text3);display:inline-block;animation:typingBounce .8s infinite}
@keyframes typingBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
#chatInput:focus{border-color:var(--accent);box-shadow:0 0 10px var(--glow)}
</style>

<script>
const SESSION_ID = 'qc_' + Math.random().toString(36).substr(2,9);
let isLoading = false;

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function getTime() {
  return new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
}

function appendMessage(text, role) {
  const wrap = document.getElementById('chatMessages');
  const isAI = role === 'ai';

  const row = document.createElement('div');
  row.className = `msg-row msg-${isAI ? 'ai' : 'user'}`;

  const avatarIcon = isAI ? '<i class="fas fa-robot"></i>' : '<?= $user ? strtoupper(substr($user["name"]??$user["username"],0,1)) : '<i class="fas fa-user"></i>' ?>';
  const avatarClass = isAI ? 'ai-avatar' : 'user-avatar';
  const bubbleClass = isAI ? 'ai-bubble' : 'user-bubble';
  const name = isAI ? 'Dabi' : '<?= $user ? htmlspecialchars($user["username"]) : "Guest" ?>';

  // Convert newlines to <br> and basic markdown
  let formatted = text
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/\n/g,'<br>')
    .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
    .replace(/\*(.*?)\*/g,'<em>$1</em>')
    .replace(/`(.*?)`/g,'<code style="background:var(--bg3);padding:.1rem .3rem;border-radius:3px;font-family:var(--font-mono);font-size:.82rem">$1</code>');

  row.innerHTML = `
    <div class="msg-avatar ${avatarClass}">${avatarIcon}</div>
    <div class="msg-bubble ${bubbleClass}">
      <div class="msg-name">${name} <span class="msg-time">${getTime()}</span></div>
      <div class="msg-text">${formatted}</div>
    </div>`;

  // Remove suggestions on first user message
  if (!isAI) {
    const sugg = document.getElementById('suggestions');
    if (sugg) sugg.remove();
  }

  wrap.appendChild(row);
  wrap.scrollTop = wrap.scrollHeight;
}

function showTyping(show) {
  document.getElementById('typingIndicator').style.display = show ? 'flex' : 'none';
  const wrap = document.getElementById('chatMessages');
  wrap.scrollTop = wrap.scrollHeight;
}

async function sendMessage() {
  if (isLoading) return;
  const input = document.getElementById('chatInput');
  const msg = input.value.trim();
  if (!msg) return;

  input.value = '';
  input.style.height = 'auto';
  appendMessage(msg, 'user');
  isLoading = true;
  showTyping(true);

  document.getElementById('sendBtn').innerHTML = '<div class="spinner" style="width:18px;height:18px;border-width:2px"></div>';

  try {
    const res = await fetch('/api/chat.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({message: msg, session_id: SESSION_ID})
    });
    const data = await res.json();
    showTyping(false);
    if (data.status) {
      appendMessage(data.reply, 'ai');
    } else {
      appendMessage('Maaf, ada error nih 😅 Coba lagi ya!', 'ai');
    }
  } catch(e) {
    showTyping(false);
    appendMessage('Koneksi bermasalah, coba lagi ya 🙏', 'ai');
  }

  isLoading = false;
  document.getElementById('sendBtn').innerHTML = '<i class="fas fa-paper-plane"></i>';
  input.focus();
}

function sendSuggestion(text) {
  document.getElementById('chatInput').value = text;
  sendMessage();
}

async function resetChat() {
  if (!confirm('Reset sesi chat?')) return;
  try {
    await fetch(`https://api.termai.cc/api/chat/logic-bell/reset?id=${SESSION_ID}&key=Bell409`);
  } catch(e) {}
  document.getElementById('chatMessages').innerHTML = `
    <div class="msg-row msg-ai">
      <div class="msg-avatar ai-avatar"><i class="fas fa-robot"></i></div>
      <div class="msg-bubble ai-bubble">
        <div class="msg-name">Dabi <span class="msg-time">${getTime()}</span></div>
        <div class="msg-text">Sesi direset! Haii lagi~ Ada yang bisa aku bantu? 😊</div>
      </div>
    </div>`;
  showToast('Sesi chat direset!');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
