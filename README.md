# MindScreen.Expert — Sistem Pakar Skrining Kesehatan Mental (Certainty Factor)

Aplikasi web sistem pakar skrining kondisi kesehatan mental berbasis metode **Certainty Factor (CF)** dengan penalaran inexact reasoning Shortliffe & Buchanan. Dibangun menggunakan arsitektur MVC standar Laravel, Blade templates, Tailwind CSS, Alpine.js, dan database MySQL (Laragon).

> **Pernyataan Penafian (Disclaimer):**  
> Sistem ini adalah instrumen skrining edukatif untuk tugas kuliah, bukan pengganti diagnosis medis resmi dari psikolog klinis atau psikiater.

---

## 1. Persyaratan Sistem & Tech Stack

- **PHP:** Versi 8.3+ (ekstensi pdo_mysql, mbstring, openssl aktif bawaan Laragon)
- **Composer:** Versi 2.8+
- **Node.js & npm:** Node.js v20+ / v22+
- **Web Server & Database:** Laragon (Apache/Nginx & MySQL 8.x / MariaDB)
- **Framework & Tooling:**
  - Laravel 13 (Blade Templates)
  - Tailwind CSS via `@tailwindcss/vite`
  - Alpine.js (Interaksi Wizard Stepper & Accordion)
  - Chart.js (Grafik Batang Distribusi Nilai CF)

---

## 2. Struktur Basis Pengetahuan & Pendekatan Hibrida

Sistem menguji 4 hipotesis kondisi mental:
- **K01:** Depresi Mayor (*Major Depressive Disorder*)
- **K02:** Gangguan Kecemasan Umum (*Generalized Anxiety*)
- **K03:** Gangguan Bipolar (*Bipolar Disorder*)
- **K04:** Gangguan Panik (*Panic Disorder*)

### Mengapa Pendekatan Hibrida?
Dataset publik Kaggle (`database/data/mental_health_diagnosis_treatment_.csv`, 500 baris) memiliki fitur numerik yang hampir identik antar ke-4 kelas diagnosis (selisih rata-rata < 0.3 poin). Menurunkan CF murni dari dataset ini akan menghasilkan bobot seragam tanpa daya pembeda. Oleh karena itu diterapkan pendekatan hibrida:
1. **Basis Aturan Klinis (DSM-5 & PPDGJ-III):** 25 gejala terstandar (G01–G25) dan 32 aturan CF pakar (bobot 0.20 – 0.90).
2. **Aturan Keselamatan Darurat (Safety Trigger):** Gejala G25 (dorongan menyakiti diri) mengaktifkan blok bantuan darurat **Kemenkes 119 ext. 8 (SEJIWA)** secara mandiri tanpa bergantung pada skor CF.
3. **Dataset Kaggle untuk Ambang Numerik & Evaluasi:** Ambang numerik (Mood $\le 4$, Sleep $\le 5$, Stress $\ge 8$) dan evaluasi jujur via `php artisan pakar:evaluasi`.

---

## 3. Langkah Instalasi di Laragon (Langkah Demi Langkah)

### Langkah 1: Penempatan Direktori Proyek
Letakkan folder proyek di dalam root web Laragon:
```text
C:\laragon\www\sistem_pakar_mental
```

### Langkah 2: Buat Database di MySQL (HeidiSQL)
1. Buka Laragon, klik **Start All**.
2. Klik tombol **Database** (membuka HeidiSQL).
3. Buat database baru bernama:
   ```sql
   CREATE DATABASE sistem_pakar_mental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### Langkah 3: Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```powershell
copy .env.example .env
```
Pastikan pengaturan database di `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_pakar_mental
DB_USERNAME=root
DB_PASSWORD=

ADMIN_EMAIL=admin@mindscreen.expert
ADMIN_PASSWORD=adminpakar2026
```

### Langkah 4: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 5: Instalasi Dependensi (Bila Belum Terpasang)
```bash
composer install
npm install
```

### Langkah 6: Migrasi Database & Seeding Awal
Jalankan migrasi tabel `users`, `conditions`, `symptoms`, `rules`, dan `consultations`:
```bash
php artisan migrate --seed
```

### Langkah 7: Sinkronisasi Basis Data & Evaluasi Dataset
Bangun basis pengetahuan dari dataset CSV ke MySQL:
```bash
php artisan pakar:bangun-data
```
Jalankan pengujian akurasi mesin CF terhadap 500 sampel dataset CSV:
```bash
php artisan pakar:evaluasi
```

### Langkah 8: Kompilasi Aset Frontend
Untuk mode produksi:
```bash
npm run build
```
Atau untuk mode pengembangan:
```bash
npm run dev
```

### Langkah 9: Akses Aplikasi
- Lewat virtual host Laragon (jika auto-virtual-host aktif):
  `http://sistem-pakar-mental.test`
- Atau jalankan development server Laravel:
  ```bash
  php artisan serve
  ```
  Lalu buka peramban di: `http://127.0.0.1:8000`

---

## 4. Akun Admin Portal Pakar

- **URL Login:** `http://127.0.0.1:8000/kelola-pakar/login`
- **Email:** `admin@mindscreen.expert`
- **Kata Sandi:** `adminpakar2026`
- **Fitur Admin:**
  - Tabel Aturan: Inline edit bobot CF pakar (0.0 – 1.0) dengan pembaruan instan.
  - Tabel Gejala: Tambah, ubah, dan hapus gejala klinis (dengan konfirmasi keamanan).
  - Tabel Kondisi: Perbarui narasi deskripsi kondisi dan butir saran umum.

---

## 5. Menjalankan Unit & Feature Testing

Sistem dilengkapi pengujian otomatis PHPUnit (15 test case) yang menguji rumus matematis Shortliffe & Buchanan, kombinasi berurutan, threshold indikasi awal, trigger keselamatan, serta seluruh respons halaman HTTP:

```bash
php vendor/phpunit/phpunit/phpunit
```
Hasil:
```text
{"tool":"phpunit","result":"passed","tests":15,"passed":15,"assertions":44,"duration_ms":1087}
OK (15 tests, 44 assertions)
```

---

## 6. Daftar Halaman & Rute Utama

| Rute URL | Nama Rute | Deskripsi |
| :--- | :--- | :--- |
| `/` | `home` | Halaman beranda, hero editorial, alur skrining, kartu kondisi K01–K04 |
| `/konsultasi` | `konsultasi.index` | Formulir skrining wizard 3 langkah dengan skala keyakinan 0.0–1.0 |
| `/hasil/{id}` | `hasil.show` | Hasil skrining, grafik batang CF, transparansi rumus hitung, cetak PDF |
| `/tentang-kami` | `about` | Penjelasan metode CF, matriks konfusi dataset, profil pengembang |
| `/kelola-pakar` | `admin.index` | Portal pengelolaan basis pengetahuan (dilindungi sesi admin) |
| `/kelola-pakar/login` | `login` | Formulir login admin sesi sederhana |
