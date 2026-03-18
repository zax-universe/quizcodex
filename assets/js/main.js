/* QuizCode — Main JS */

// ---- MATRIX RAIN ----
(function() {
  const canvas = document.getElementById('matrixCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  const chars = 'アイウエオカキクケコサシスセソタチツテトナニヌネノ0123456789ABCDEF{}[]<>/\\|;:=+-*#@!$%^&';
  let drops = [];
  const fontSize = 14;

  function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    const cols = Math.floor(canvas.width / fontSize);
    drops = Array(cols).fill(1);
  }
  resize();
  window.addEventListener('resize', resize);

  function draw() {
    ctx.fillStyle = 'rgba(8,12,16,0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff88';
    ctx.font = fontSize + 'px JetBrains Mono, monospace';
    drops.forEach((y, i) => {
      const char = chars[Math.floor(Math.random() * chars.length)];
      ctx.fillText(char, i * fontSize, y * fontSize);
      if (y * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
      drops[i]++;
    });
  }
  setInterval(draw, 60);
})();

// ---- MOBILE MENU ----
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
if (hamburger && mobileMenu) {
  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
  });
}

// ---- TOAST NOTIFICATIONS ----
function showToast(msg, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
  toast.innerHTML = `<i class="fas ${icon}"></i> ${msg}`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3200);
}

// ---- PROFILE TABS ----
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const target = document.getElementById(btn.dataset.tab);
    if (target) target.classList.add('active');
  });
});

// ---- AVATAR PREVIEW ----
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
if (avatarInput && avatarPreview) {
  avatarInput.addEventListener('change', () => {
    const file = avatarInput.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => { avatarPreview.src = e.target.result; };
      reader.readAsDataURL(file);
    }
  });
}

// ---- QUIZ ENGINE ----
const QuizEngine = {
  questions: [],
  current: 0,
  score: 0,
  answered: false,
  timer: null,
  timeLeft: 20,
  total: 10,

  async init() {
    const startBtn = document.getElementById('startQuizBtn');
    if (startBtn) {
      startBtn.addEventListener('click', () => this.start());
    }
  },

  async fetchQuestion() {
    try {
      const r = await fetch('/api/question.php');
      const d = await r.json();
      if (d.status) return d.result;
    } catch (e) { console.error(e); }
    return null;
  },

  async start() {
    document.getElementById('quizStart').style.display   = 'none';
    document.getElementById('quizGame').style.display    = 'block';
    document.getElementById('quizResult').style.display  = 'none';
    this.questions = [];
    this.current   = 0;
    this.score     = 0;
    this.answered  = false;
    await this.loadQuestion();
  },

  async loadQuestion() {
    this.answered = false;
    clearInterval(this.timer);
    this.timeLeft = 20;

    const q = await this.fetchQuestion();
    if (!q) { showToast('Gagal memuat soal, coba lagi', 'error'); return; }
    this.questions.push(q);

    // Progress
    const prog = document.getElementById('quizProgress');
    if (prog) prog.style.width = ((this.current / this.total) * 100) + '%';
    document.getElementById('qNum').textContent   = `Soal ${this.current + 1}/${this.total}`;
    document.getElementById('qScore').textContent = this.score;
    document.getElementById('qText').textContent  = q.question;
    document.getElementById('qCat').textContent   = q.category;

    // Options
    const container = document.getElementById('optionsContainer');
    container.innerHTML = '';
    ['a','b','c','d'].forEach(key => {
      if (!q.options[key]) return;
      const btn = document.createElement('button');
      btn.className   = 'option-btn';
      btn.dataset.key = key;
      btn.innerHTML   = `<span class="opt-key">${key}</span> <span>${q.options[key]}</span>`;
      btn.addEventListener('click', () => this.answer(key, q.answer));
      container.appendChild(btn);
    });

    // Feedback clear
    const fb = document.getElementById('feedback');
    if (fb) { fb.className = 'feedback-msg'; fb.textContent = ''; }

    // Next button
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) nextBtn.style.display = 'none';

    // Timer
    this.startTimer();
  },

  startTimer() {
    const el = document.getElementById('timerDisplay');
    this.timer = setInterval(() => {
      this.timeLeft--;
      if (el) {
        el.textContent = this.timeLeft;
        el.parentElement.className = 'timer-ring' + (this.timeLeft <= 5 ? ' danger' : '');
      }
      if (this.timeLeft <= 0) {
        clearInterval(this.timer);
        if (!this.answered) this.answer(null, this.questions[this.current]?.answer);
      }
    }, 1000);
  },

  answer(chosen, correct) {
    if (this.answered) return;
    this.answered = true;
    clearInterval(this.timer);

    const btns = document.querySelectorAll('.option-btn');
    btns.forEach(btn => {
      btn.disabled = true;
      if (btn.dataset.key === correct) btn.classList.add('correct');
      else if (btn.dataset.key === chosen) btn.classList.add('wrong');
    });

    const fb = document.getElementById('feedback');
    if (chosen === correct) {
      this.score++;
      document.getElementById('qScore').textContent = this.score;
      if (fb) { fb.className = 'feedback-msg correct'; fb.innerHTML = '<i class="fas fa-check-circle"></i> Benar! Good job!'; }
    } else {
      if (fb) { fb.className = 'feedback-msg wrong'; fb.innerHTML = `<i class="fas fa-times-circle"></i> Salah! Jawaban: <strong>${correct.toUpperCase()}</strong>`; }
    }

    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) nextBtn.style.display = 'inline-flex';
    nextBtn.onclick = () => {
      this.current++;
      if (this.current >= this.total) this.finish();
      else this.loadQuestion();
    };
  },

  async finish() {
    clearInterval(this.timer);
    document.getElementById('quizGame').style.display   = 'none';
    document.getElementById('quizResult').style.display = 'block';

    const pct = Math.round((this.score / this.total) * 100);
    document.getElementById('finalScore').textContent   = pct + '%';
    document.getElementById('finalCorrect').textContent = this.score + '/' + this.total;

    let grade = 'F', gradeColor = '#ff4d6d';
    if (pct >= 90) { grade = 'A+'; gradeColor = '#00ff88'; }
    else if (pct >= 80) { grade = 'A'; gradeColor = '#00ff88'; }
    else if (pct >= 70) { grade = 'B'; gradeColor = '#0af'; }
    else if (pct >= 60) { grade = 'C'; gradeColor = '#f0c040'; }
    else if (pct >= 50) { grade = 'D'; gradeColor = '#f0c040'; }
    document.getElementById('finalGrade').textContent = grade;
    document.getElementById('finalGrade').style.color = gradeColor;

    // Save score
    try {
      await fetch('/api/save_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ score: this.score, total: this.total, category: 'Random' })
      });
    } catch (e) {}
  },

  playAgain() {
    this.start();
  }
};

document.addEventListener('DOMContentLoaded', () => {
  QuizEngine.init();
});
      
