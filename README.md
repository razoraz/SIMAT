# 🏥 SIMAT (Sistem Informasi Manajemen Aset & Logistik)

Aplikasi manajemen aset, inventarisasi, dan logistik rumah sakit berbasis web yang dibangun menggunakan **Laravel** dan **Tailwind / Vite**.

---

## 📋 Daftar Isi
- [Prasyarat Sistem](#-prasyarat-sistem)
- [Panduan Instalasi](#-panduan-instalasi)
- [Akun Pengguna Default](#-akun-pengguna-default)
- [Fitur Utama](#-fitur-utama)
- [Troubleshooting](#-troubleshooting)

---

## ⚙️ Prasyarat Sistem

Sebelum menjalankan aplikasi, pastikan perangkat Anda telah terpasang:
- **PHP** >= 8.2 atau 8.3 (dengan ekstensi `pdo_sqlite` / `pdo_mysql`, `mbstring`, `openssl`)
- **Composer** ([getcomposer.org](https://getcomposer.org/))
- **Node.js** (v18+) & **NPM** ([nodejs.org](https://nodejs.org/))
- **Git** ([git-scm.com](https://git-scm.com/))
- *Database*: SQLite (default) atau MySQL / MariaDB (melalui XAMPP/Laragon)

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan aplikasi dari nol:

### 1. Clone Repository
```bash
git clone https://github.com/razoraz/SIMAT.git
cd SIMAT
```

### 2. Install Dependensi (PHP & Node.js)
```bash
composer install
npm install
```

### 3. Konfigurasi Environment File
Salin template `.env.example` menjadi `.env`:

**Windows (PowerShell / CMD):**
```bash
copy .env.example .env
```

**Linux / macOS / Git Bash:**
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Konfigurasi & Migrasi Database
Aplikasi ini secara default menggunakan database **SQLite**.

Jalankan perintah migrasi beserta data awal (*seeder*):
```bash
php artisan migrate --seed
```
> *Catatan: Jika muncul pertanyaan untuk membuat file database SQLite (`database.sqlite`), ketik `yes`.*

### 6. Build Asset Frontend & Jalankan Server

Jalankan server aplikasi Laravel:
```bash
php artisan serve
```

Di jendela / tab terminal terpisah, jalankan server pengembangan Vite:
```bash
npm run dev
```
*(Atau gunakan `npm run build` untuk build produksi sekali saja)*

Buka browser dan akses: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** atau **[http://localhost:8000](http://localhost:8000)**.

---

## 👤 Akun Pengguna Default

Setelah menjalankan `php artisan migrate --seed`, akun berikut siap digunakan untuk login:

| Role | Email | Password | Deskripsi / Hak Akses |
| :--- | :--- | :--- | :--- |
| **Master Admin** | `masteradmin@asimat.com` | `password123` | Akses penuh seluruh master data, konfigurasi pengguna, dan laporan eksekutif. |
| **Admin** | `admin@asimat.com` | `password123` | Akses operasional harian: Pengadaan, Distribusi, Mutasi, Pemeliharaan, dan Berita Acara. |
| **Sub Admin** | `subadmin@asimat.com` | `password123` | Akses ruangan / sub-unit untuk pengajuan permohonan aset dan pemantauan barang unit. |

---

## ✨ Fitur Utama

- 🔐 **Autentikasi & Multi-Role Authorization** (Master Admin, Admin, Sub Admin)
- 📦 **Manajemen Master Data Aset**:
  - Master Jenis Aset (ASTAP) & Kodefikasi BMD 108
  - Master Jenis Pengadaan & Unit Paviliun / Ruangan
- 🔄 **Modul Operasional Aset**:
  - Form & Data Pengadaan Aset
  - Form & Data Distribusi Aset
  - Form & Data Mutasi Aset Antar Unit
  - Form & Riwayat Pemeliharaan Aset
  - Form & Pencetakan Berita Acara
- 📊 **Dashboard Interaktif**:
  - Statistik ringkasan aset realtime per kategori dan per role pengguna.

---

## 🛠️ Troubleshooting

- **File SQLite Tidak Ditemukan**:
  Buat file database kosong secara manual, lalu migrasi ulang:
  ```bash
  # Windows PowerShell
  New-Item -ItemType File -Path database/database.sqlite -Force
  php artisan migrate:fresh --seed
  ```
- **Tampilan CSS / Ikon Tidak Muncul**:
  Pastikan build frontend telah selesai:
  ```bash
  npm run build
  ```
- **Menghapus Cache Konfigurasi**:
  ```bash
  php artisan optimize:clear
  ```

---

## 📄 Lisensi
Proyek ini dikembangkan untuk kebutuhan internal RSUD dr. H. Koesnandi Bondowoso.
