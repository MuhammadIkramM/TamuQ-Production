<h1 align="center">
  <br>
  🏛️ TamuQ Backend API Service
  <br>
</h1>

<p align="center">
  <strong>Robust RESTful API for Smart Library Guestbook & Management System</strong>
  <br>
  <em>Pusat Data & Logika Bisnis — Sistem Manajemen Perpustakaan JTIK UNM</em>
</p>

<p align="center">
  <!-- Tech Stack Badges -->
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2"/>
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12"/>
  <img src="https://img.shields.io/badge/PostgreSQL-Database-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL"/>
  <img src="https://img.shields.io/badge/Laravel_Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Sanctum"/>
  <img src="https://img.shields.io/badge/Docker-Container-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker"/>
  <img src="https://img.shields.io/badge/Render-Deployed-46E3B7?style=for-the-badge&logo=render&logoColor=white" alt="Render"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Status-Production_Ready-brightgreen?style=flat-square" alt="Status"/>
  <img src="https://img.shields.io/badge/API_Version-v1.0-blue?style=flat-square" alt="API Version"/>
  <img src="https://img.shields.io/badge/HKI-Terdaftar_DJKI-gold?style=flat-square" alt="HKI"/>
  <img src="https://img.shields.io/badge/Institusi-JTIK_UNM-red?style=flat-square" alt="JTIK UNM"/>
</p>

---

## 📖 Tentang Repositori Ini

Repositori ini adalah **otak (backend service)** dari sistem **TamuQ** — platform manajemen perpustakaan cerdas yang dikembangkan untuk Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM).

Backend ini dibangun di atas **Laravel 12** dan berfungsi sebagai pusat data serta penyedia RESTful API yang dikonsumsi oleh aplikasi frontend (Vue 3). Seluruh operasi sistem — mulai dari autentikasi pengguna, pencatatan kunjungan melalui pemindaian QR Code, sirkulasi peminjaman buku, hingga validasi pengajuan surat bebas pustaka — diproses dan dikelola di sini.

### 🎯 Apa yang Dilayani API Ini?

| Modul | Keterangan |
|---|---|
| 🔐 **Autentikasi** | Login, registrasi, dan manajemen sesi berbasis Laravel Sanctum |
| 📱 **Kunjungan QR** | Generate token QR satu-kali-pakai & verifikasi scan oleh staff |
| 📚 **Sirkulasi Buku** | Pencatatan peminjaman, pengembalian, dan riwayat per user |
| 📄 **Pengajuan Surat** | Pengajuan dan verifikasi surat bebas pustaka & donasi buku |
| 👤 **Manajemen User** | CRUD akun pengguna khusus role `superadmin` |
| 📊 **Dashboard Staff** | Statistik pengunjung harian, stok buku, dan status perpustakaan |

---

## 🏗️ Arsitektur & Modul API

Sistem ini mengimplementasikan **Role-Based Access Control (RBAC)** dengan tiga tingkatan hak akses:

```
┌─────────────────────────────────────────────┐
│              TamuQ API Gateway              │
│         (Laravel 12 + Sanctum Auth)         │
├──────────────┬──────────────┬───────────────┤
│  🧑 pengunjung │  👮 staff     │  🛡️ superadmin │
│  (Publik Auth)│  (Role:staff) │  (Role:admin)  │
└──────────────┴──────────────┴───────────────┘
```

### 📂 Struktur Controller

```
app/Http/Controllers/
├── Api/
│   ├── AuthController.php          # Login, Register, Profile, Password
│   ├── QrCodeController.php        # Generate QR Token (30 detik TTL)
│   ├── PengajuanSuratController.php# Pengajuan & Verifikasi Surat
│   └── AdminUserController.php     # CRUD User (superadmin only)
├── VisitController.php             # Scan QR & Riwayat Kunjungan
├── PeminjamanController.php        # Sirkulasi Peminjaman Buku
├── BukuController.php              # Manajemen Katalog Buku
└── DashboardController.php         # Statistik & Status Perpustakaan
```

### 🗃️ Entitas Database (Models)

```
app/Models/
├── User.php                # Tabel: users       | PK: id_user
├── QrToken.php             # Tabel: qr_tokens   | PK: id
├── Kunjungan.php           # Tabel: kunjungan   | FK: user_id
├── Buku.php                # Tabel: buku        | PK: id_buku
├── Peminjaman.php          # Tabel: peminjaman  | PK: id_peminjaman
├── RiwayatPengembalian.php # Tabel: riwayat_pengembalian
└── PengajuanSurat.php      # Tabel: pengajuan_surats | FK: user_id
```

---

## 📡 Referensi Endpoint API

> **Base URL (Production):** `https://tamuq-api.onrender.com/api`
> 
> **Base URL (Lokal):** `http://localhost:8000/api`

Semua request yang memerlukan autentikasi harus menyertakan header:
```
Accept: application/json
```

---

### 🔐 Modul 1 — Autentikasi (Public)

Endpoint berikut **tidak memerlukan token** dan dapat diakses langsung.

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| `POST` | `/api/login` | Login dengan `nomor_induk` dan `password` |
| `POST` | `/api/register` | Registrasi akun baru (role default: `pengunjung`) |

#### `POST /api/login`

**Request Body:**
```json
{
  "nomor_induk": "220209501001",
  "password": "password_anda"
}
```

**Response Sukses (`200 OK`):**
```json
{
  "success": true,
  "message": "Login Berhasil",
  "data": {
    "user": {
      "id_user": 1,
      "nama_lengkap": "Muhammad Ikram",
      "nomor_induk": "220209501001",
      "prodi": "PTIK",
      "role": "pengunjung",
      "status_akun": "aktif",
      "foto_profil": null
    },
    "role": "pengunjung"
  }
}
```

> ⚠️ **Rate Limiting:** Percobaan login dibatasi **50 kali per IP**. Melampaui batas akan mengembalikan HTTP `429`.

---

### 👤 Modul 2 — Profil & Sesi (Auth Required)

Semua endpoint di bawah memerlukan sesi aktif (cookie Sanctum).

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| `POST` | `/api/logout` | Mengakhiri sesi login |
| `GET`  | `/api/user` | Mengambil data profil user yang sedang login |
| `POST` | `/api/user/update-profile` | Memperbarui nama dan/atau foto profil (`multipart/form-data`) |
| `POST` | `/api/user/update-password` | Mengubah password (memerlukan `current_password`) |

**Request Body `update-profile` (`multipart/form-data`):**
```
nama_lengkap : string (opsional)
foto         : file image (max 5MB, opsional)
```

**Request Body `update-password`:**
```json
{
  "current_password": "password_lama",
  "new_password": "password_baru",
  "new_password_confirmation": "password_baru"
}
```

---

### 📱 Modul 3 — Kunjungan & QR Code (Auth Required)

Alur kunjungan: **Pengunjung generate QR → Staff scan QR → Kunjungan tercatat.**

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`  | `/api/qr/generate` | `pengunjung` | Generate token QR satu-kali-pakai (TTL: **30 detik**) |
| `POST` | `/api/kunjungan/scan` | `staff` | Verifikasi & catat kunjungan berdasarkan token QR |
| `GET`  | `/api/kunjungan/history` | semua | Riwayat kunjungan (staff: semua user, pengunjung: milik sendiri) |
| `GET`  | `/api/library-status` | semua | Cek status buka/tutup perpustakaan |

**Response `GET /api/qr/generate` (`200 OK`):**
```json
{
  "status": "success",
  "qr_code": "Kj3xLmNpQ7rStUvWxYzAb...",
  "expires_in": 30
}
```

**Request Body `POST /api/kunjungan/scan`:**
```json
{
  "qr_code": "Kj3xLmNpQ7rStUvWxYzAb..."
}
```

**Response Sukses Scan (`200 OK`):**
```json
{
  "status": "success",
  "message": "Scan Berhasil",
  "data": {
    "nama_lengkap": "Muhammad Ikram",
    "nomor_induk": "220209501001",
    "prodi": "PTIK",
    "foto": null,
    "waktu": "2026-06-07 14:30:00"
  }
}
```

---

### 📚 Modul 4 — Katalog & Sirkulasi Buku (Auth Required)

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`    | `/api/buku` | semua | Mengambil seluruh daftar buku di katalog |
| `POST`   | `/api/buku` | `staff` | Menambahkan buku baru ke katalog |
| `PUT`    | `/api/buku/{id}` | `staff` | Memperbarui data buku |
| `DELETE` | `/api/buku/{id}` | `staff` | Menghapus buku dari katalog |
| `GET`    | `/api/user/buku-saya` | `pengunjung` | Melihat daftar buku yang sedang/pernah dipinjam |
| `GET`    | `/api/staff/peminjaman` | `staff` | Daftar peminjaman aktif + riwayat pengembalian |
| `POST`   | `/api/staff/peminjaman` | `staff` | Mencatat transaksi peminjaman buku baru |
| `POST`   | `/api/staff/peminjaman/{id}/return` | `staff` | Mencatat pengembalian buku |
| `GET`    | `/api/staff/users-list` | `staff` | Daftar user untuk keperluan form peminjaman |

**Request Body `POST /api/staff/peminjaman`:**
```json
{
  "id_user": 5,
  "id_buku": 12,
  "tanggalPinjam": "2026-06-07",
  "tanggalKembali": "2026-06-14",
  "noHp": "081234567890"
}
```

> 📌 **Catatan:** Sistem menggunakan **database transaction dengan lock** saat proses peminjaman untuk mencegah race condition pada stok buku yang sama.

---

### 📄 Modul 5 — Pengajuan Surat (Auth Required)

Mendukung dua jenis pengajuan: **Bebas Perpustakaan** dan **Donasi Buku**. Khusus donasi buku, sistem secara otomatis mengirim notifikasi ke Google Spreadsheet via webhook.

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`  | `/api/pengajuan` | semua | Daftar pengajuan (staff: semua, pengunjung: milik sendiri) |
| `POST` | `/api/pengajuan` | `pengunjung` | Membuat pengajuan baru (upload `file_pendukung`) |
| `POST` | `/api/pengajuan/{id}/verify` | `staff` | Verifikasi pengajuan + upload `file_balasan` |
| `GET`  | `/api/download-pendukung/{filename}` | semua | Unduh file dokumen pendukung |
| `GET`  | `/api/download-balasan/{filename}` | semua | Unduh file surat balasan dari staff |

**Request Body `POST /api/pengajuan` (`multipart/form-data`):**
```
jenis          : "Bebas Perpustakaan" | "Donasi Buku"  (wajib)
file_pendukung : file PDF/JPG/PNG, max 5MB              (wajib)
no_hp          : string                                  (opsional)
alamat         : string                                  (opsional)
jabatan        : string                                  (opsional)
judul_buku     : string, jika Donasi Buku               (opsional)
```

**Status Alur Pengajuan:**

```
[Menunggu Verifikasi] → [Selesai] (jika diterima + file balasan diupload)
                      ↘ [Ditolak] (jika ditolak oleh staff)
```

---

### 🛡️ Modul 6 — Manajemen User (Superadmin Only)

Endpoint ini **eksklusif** untuk akun dengan role `superadmin`.

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| `GET`    | `/api/admin/users` | Mengambil seluruh daftar user yang terdaftar |
| `POST`   | `/api/admin/users` | Membuat akun user baru dengan role tertentu |
| `PUT`    | `/api/admin/users/{id}` | Memperbarui data & status akun user |
| `DELETE` | `/api/admin/users/{id}` | Menghapus akun user dari sistem |

---

### 📊 Modul 7 — Dashboard Staff (Role: staff)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| `GET`  | `/api/staff/dashboard` | Statistik lengkap: total buku, pengunjung hari ini, peminjaman aktif, data chart |
| `POST` | `/api/staff/library-status` | Toggle status buka/tutup perpustakaan |

**Response `GET /api/staff/dashboard`:**
```json
{
  "is_open": true,
  "total_buku": 245,
  "pengunjung_hari_ini": 38,
  "peminjaman_aktif": 12,
  "chart_data": [5, 12, 8, 9, 4],
  "demografi": [180, 65]
}
```

---

## ⚡ Panduan Instalasi Lokal (Quick Start)

### Prasyarat

Pastikan lingkungan pengembangan Anda sudah memiliki:
- **PHP** `>= 8.2`
- **Composer** `>= 2.x`
- **PostgreSQL** (atau MySQL, sesuaikan `.env`)
- **Git**

### Langkah Instalasi

```bash
# 1. Clone repositori ini
git clone <url-repositori-ini>
cd tamuq-backend

# 2. Install semua dependensi PHP via Composer
composer install

# 3. Buat file konfigurasi environment dari template
cp .env.example .env
```

> 📝 **Penting:** Buka file `.env` yang baru dibuat, lalu isi kredensial database Anda:
> ```dotenv
> DB_CONNECTION=pgsql
> DB_HOST=127.0.0.1
> DB_PORT=5432
> DB_DATABASE=tamuq_db
> DB_USERNAME=postgres
> DB_PASSWORD=password_anda
> ```

```bash
# 4. Generate application key
php artisan key:generate

# 5. Jalankan migrasi database (dan seeder jika tersedia)
php artisan migrate --seed

# 6. Buat symlink storage untuk akses file publik
php artisan storage:link

# 7. Jalankan server pengembangan lokal
php artisan serve
```

API Anda sekarang berjalan di: **`http://localhost:8000`** 🎉

---

## 🚀 Kesiapan Deployment (Cloud-Ready)

Backend ini telah dikonfigurasi secara penuh untuk deployment production di lingkungan cloud. Terdapat dua berkas konfigurasi deployment di root direktori:

### 🐳 `Dockerfile` — Containerization

Berkas `Dockerfile` mendefinisikan cara membungkus seluruh aplikasi Laravel beserta semua dependensinya ke dalam sebuah **Docker container** yang portabel dan konsisten. Konfigurasi ini mencakup:

- **Base Image:** `php:8.2-apache` — PHP 8.2 dengan server Apache
- **Driver Database:** Menginstal ekstensi `pdo_pgsql` untuk koneksi PostgreSQL
- **Web Server:** Mengaktifkan `mod_rewrite` Apache untuk mendukung URL routing Laravel
- **Composer:** Dependensi di-install dengan flag `--no-dev --optimize-autoloader` untuk performa production
- **Permissions:** Folder `storage/` dan `bootstrap/cache/` dikonfigurasi agar dapat ditulis oleh server

```bash
# Build Docker image secara manual
docker build -t tamuq-backend .

# Jalankan container
docker run -p 8000:80 --env-file .env tamuq-backend
```

---

### ☁️ `render.yaml` — Otomatisasi Deployment ke Render

Berkas `render.yaml` adalah **Infrastructure as Code** untuk platform [Render.com](https://render.com). File ini memungkinkan Render secara otomatis mendeteksi konfigurasi layanan tanpa perlu pengaturan manual via dashboard.

```yaml
# Konfigurasi aktif render.yaml:
services:
  - type: web
    name: tamuq-api
    env: php
    region: singapore         # Server di region Asia Tenggara
    plan: free
    buildCommand: composer install --no-dev --optimize-autoloader
    startCommand: php artisan serve --host=0.0.0.0 --port=10000
```

> 💡 **Variabel Lingkungan Sensitif** seperti `APP_KEY` dan `DATABASE_URL` dikonfigurasi langsung di dashboard Render (tidak di-commit ke repositori) dengan flag `sync: false`.

---

## 🔒 Keamanan & Autentikasi

### Laravel Sanctum (Cookie-Based Auth)

API ini menggunakan **Laravel Sanctum** sebagai mekanisme autentikasi. Sanctum dipilih karena kemampuannya mendukung **SPA (Single Page Application) Authentication** melalui cookie sesi yang HttpOnly dan aman — ideal untuk frontend berbasis Vue 3.

Alur autentikasi:
1. Frontend melakukan `POST /api/login` dengan kredensial.
2. Sanctum mengeluarkan **session cookie** yang disimpan di browser.
3. Setiap request berikutnya secara otomatis menyertakan cookie tersebut.
4. Middleware `auth:sanctum` memvalidasi cookie di setiap endpoint terproteksi.

### Middleware Keamanan Tambahan

| Mekanisme | Detail |
|-----------|--------|
| **Rate Limiting** | Login & registrasi dibatasi 50 percobaan/IP untuk mencegah brute-force |
| **Role Middleware** | `role:staff` dan `role:superadmin` memisahkan akses secara ketat |
| **Input Validation** | Semua input divalidasi dengan `Validator` Laravel + regex sanitasi |
| **File Security** | File upload hanya menerima `pdf`, `jpg`, `jpeg`, `png` dengan ukuran max 5MB |
| **DB Transaction** | Operasi kritis (peminjaman) menggunakan `DB::beginTransaction()` + row lock |

---

## 📋 Ringkasan Kode HTTP Respons

| Kode | Arti |
|------|------|
| `200` | OK — Request berhasil diproses |
| `201` | Created — Data baru berhasil dibuat |
| `400` | Bad Request — QR sudah dipakai / stok habis |
| `401` | Unauthorized — Sesi tidak valid / belum login |
| `403` | Forbidden — Hak akses tidak mencukupi |
| `404` | Not Found — Data yang diminta tidak ditemukan |
| `422` | Unprocessable Entity — Validasi input gagal |
| `429` | Too Many Requests — Rate limit terlampaui |
| `500` | Internal Server Error — Kesalahan di sisi server |

---

## ⚖️ Lisensi & Hak Kekayaan Intelektual

> **⚠️ Hak Cipta Dilindungi Undang-Undang**

Source code backend service ini merupakan bagian integral dari **Sistem TamuQ — Smart Library Guestbook & Management System**, sebuah karya cipta intelektual yang dikembangkan oleh tim pengembang **Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM)**.

Sistem ini telah **terdaftar secara resmi sebagai Program Komputer** di **Direktorat Jenderal Kekayaan Intelektual (DJKI), Kementerian Hukum dan HAM Republik Indonesia**.

Segala bentuk reproduksi, distribusi, modifikasi, atau penggunaan komersial atas kode sumber ini tanpa izin tertulis dari pemilik hak cipta adalah **dilarang keras** dan dapat dikenakan sanksi hukum sesuai ketentuan yang berlaku.

---

<p align="center">
    Dikembangkan oleh Muhammad Ikram Muslimin.
  <br>
  <em>© 2024–2026 TamuQ System. All Rights Reserved.</em>
</p>
