# 🖥️ QuizCode — Platform Quiz Pilihan Ganda

Platform kuis tentang codink sains dan teknology

---

## 📁 Struktur Direktori

```
quizcode/
├── index.php              # Homepage
├── .htaccess              # Apache config + keamanan
├── api/
│   ├── question.php       # Fetch soal dari API eksternal
│   ├── save_score.php     # Simpan skor user
│   └── logout.php         # Logout handler
├── assets/
│   ├── css/main.css       # Semua styling
│   └── js/main.js         # Quiz engine + interaksi
├── data/
│   ├── users.json         # Database user (auto-generated)
│   └── scores.json        # Database skor (auto-generated)
├── includes/
│   ├── auth.php           # Fungsi autentikasi
│   ├── db.php             # Database helper (JSON)
│   ├── header.php         # Navbar + head HTML
│   └── footer.php         # Footer + script tag
├── pages/
│   ├── login.php          # Halaman login
│   ├── register.php       # Halaman register
│   ├── quiz.php           # Halaman quiz
│   ├── profile.php        # Profil + edit + statistik
│   └── leaderboard.php    # Leaderboard global
└── uploads/
    └── avatars/           # Foto profil user
```

---

## ⚙️ Cara Install

### Kebutuhan
- PHP 7.4+ (rekomendasi PHP 8.x)
- Apache/Nginx dengan mod_rewrite
- Akses internet (untuk API soal)

### Langkah

1. **Upload semua file** ke root web server (misal: `/var/www/html/` atau `public_html/`)

2. **Set permission folder data & uploads:**
```bash
chmod 755 data/
chmod 755 uploads/
chmod 755 uploads/avatars/
chmod 644 data/users.json
chmod 644 data/scores.json
```

3. **Aktifkan mod_rewrite** (Apache):
```bash
a2enmod rewrite
service apache2 restart
```

4. **Pastikan AllowOverride All** di Apache config:
```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

5. **Buka browser** dan akses domain/IP kamu → selesai!

---

## 🌐 Untuk Localhost (XAMPP/Laragon)

1. Extract ke folder `htdocs/quizcode/` (XAMPP) atau `www/quizcode/` (Laragon)
2. Akses via `http://localhost/quizcode/`
3. File `.htaccess` sudah dikonfigurasi

---

## 🔐 Keamanan

- Password di-hash dengan **bcrypt** (PHP `password_hash`)
- File `data/*.json` diproteksi via `.htaccess` (tidak bisa diakses langsung)
- Folder `includes/` juga diblokir akses publik
- Upload avatar divalidasi ekstensi & ukuran (max 2MB)
- Session PHP standar

---

## 🎮 Fitur

| Fitur | Keterangan |
|-------|-----------|
| Quiz 10 Soal | Soal random dari API real-time |
| Timer 20 Detik | Per soal, otomatis submit jika habis |
| Guest Mode | Bisa lihat tanpa login, tapi skor tidak tersimpan |
| Register/Login | Autentikasi lengkap |
| Edit Profil | Nama, username, email, bio, foto profil |
| Riwayat Quiz | Semua sesi quiz tersimpan |
| Statistik | Distribusi grade, rata-rata, skor terbaik |
| Leaderboard | Ranking global berdasarkan total skor |
| Matrix Rain | Efek background animasi |
| Responsive | Mobile-friendly |

---

## 📡 API Soal

Menggunakan API dari IkyyOfficial:
```
GET https://ikyyzyyrestapi.my.id/games/pilihanganda
```

Response:
```json
{
  "status": true,
  "creator": "IkyyOfficial",
  "result": {
    "id": 44,
    "category": "Random",
    "question": "Keyboard termasuk perangkat?",
    "options": { "a": "Output", "b": "Input", "c": "Storage", "d": "Process" },
    "answer": "b"
  }
}
```

> Jika API tidak bisa diakses, sistem akan menggunakan soal fallback bawaan.

---

## 🛠️ Kustomisasi

### Ganti warna tema
Edit variabel CSS di `assets/css/main.css`:
```css
:root {
  --accent:  #00ff88;  /* Warna utama hijau */
  --accent2: #0af;     /* Warna biru */
  --accent3: #ff4d6d;  /* Warna merah */
  --bg:      #080c10;  /* Background utama */
}
```

### Ganti jumlah soal per sesi
Edit di `assets/js/main.js`:
```js
total: 10,  // Ganti angka ini
```

### Ganti timer per soal
```js
this.timeLeft = 20;  // Dalam detik
```

---

## 📝 Catatan

- Database menggunakan **JSON flat file** — cocok untuk skala kecil-menengah
- Untuk skala besar, migrasi ke MySQL/PostgreSQL disarankan
- File JSON tersimpan di folder `data/` dan dibuat otomatis saat pertama run

---

Made with ❤️ — zax-universe v1.0
