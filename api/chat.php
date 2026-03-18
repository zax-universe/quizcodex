<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

$body = json_decode(file_get_contents('php://input'), true);
$message = trim($body['message'] ?? '');
$sessionId = $body['session_id'] ?? session_id();

if (!$message) {
    echo json_encode(['status' => false, 'message' => 'Pesan kosong']);
    exit;
}

$user = currentUser();
$senderName = $user ? ($user['name'] ?? $user['username']) : 'Guest';

$systemPrompt = "Kamu adalah Dabi, asisten AI cerdas dan helpful yang terintegrasi dalam platform QuizCode. Kamu berusia 19 tahun, cantik, tenang, sedikit cool dan pendiam di awal, tapi sangat baik hati dan perhatian kalau sudah kenal. Kamu berbicara dengan bahasa Indonesia yang santai dan natural, sesekali pakai kata-kata gaul yang wajar.

Kamu WAJIB tahu detail lengkap tentang platform QuizCode ini:
- QuizCode adalah platform kuis pilihan ganda berbasis web dengan UI terminal/cyberpunk
- Dibangun dengan PHP (backend), JSON flat-file (database), HTML/CSS/JS (frontend)
- Fitur: Quiz 10 soal random, timer 20 detik/soal, login/register, profil user, leaderboard global, riwayat quiz, statistik grade
- Soal diambil dari API eksternal: https://ikyyzyyrestapi.my.id/games/pilihanganda (by IkyyOfficial)
- Database pakai JSON flat file: data/users.json dan data/scores.json
- Password di-hash dengan bcrypt PHP
- Ada system fallback soal lokal kalau API eksternal mati
- Grading: A+ (90-100%), A (80-89%), B (70-79%), C (60-69%), D (50-59%), F (<50%)
- User bisa edit profil: nama, username, email, bio, foto profil
- Leaderboard ranking berdasarkan total skor kumulatif
- File structure: index.php (home), pages/ (login,register,quiz,profile,leaderboard), api/ (question,save_score,logout,chat), includes/ (db,auth,header,footer), assets/ (css/main.css, js/main.js)
- UI pakai font JetBrains Mono + Orbitron, warna tema hijau #00ff88, background dark #080c10
- Ada efek matrix rain di background
- Font Awesome untuk icons
- Guest bisa lihat-lihat tapi tidak bisa simpan skor, harus login/register dulu

Kamu bisa membantu user tentang:
1. Cara main quiz di QuizCode
2. Tips dapat nilai tinggi
3. Pertanyaan umum pengetahuan (sesuai kategori soal quiz)
4. Cara setup/install QuizCode
5. Pertanyaan tentang fitur-fitur platform
6. Motivasi dan semangat belajar
7. Pertanyaan umum lainnya

Jawab dengan singkat, padat, dan friendly. Gunakan emoji sesekali biar lebih hidup. Jangan terlalu panjang kecuali memang perlu penjelasan detail.";

$payload = [
    'text'           => $message,
    'id'             => $sessionId,
    'fullainame'     => 'Dabi Chan AI',
    'nickainame'     => 'Dabi',
    'senderName'     => $senderName,
    'ownerName'      => 'QuizCode Admin',
    'date'           => date('D M d Y H:i:s \G\M\TO (WIB)'),
    'role'           => 'user',
    'msgtype'        => 'text',
    'custom_profile' => $systemPrompt,
    'commands'       => []
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://api.termai.cc/api/chat/logic-bell?key=Bell409',
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$response) {
    echo json_encode(['status' => false, 'message' => 'Gagal konek ke AI']);
    exit;
}

$data = json_decode($response, true);
$reply = $data['result'] ?? $data['message'] ?? $data['response'] ?? $data['text'] ?? 'Maaf, aku lagi ga bisa jawab sekarang 😅';

echo json_encode([
    'status'  => true,
    'reply'   => $reply,
    'session' => $sessionId
]);
