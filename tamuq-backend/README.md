<h1 align="center">
  <br>
  🏛️ TamuQ Backend API Service
  <br>
</h1>

<p align="center">
  <strong>Robust RESTful API for Smart Library Guestbook & Management System</strong>
  <br>
  <em>Pusat Data, Document Streaming & Logika Bisnis — Sistem Manajemen Perpustakaan JTIK UNM</em>
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
  <a href="#️-hak-cipta"><img src="https://img.shields.io/badge/HKI-Terdaftar_DJKI-gold?style=flat-square" alt="HKI"/></a>
  <a href="./LICENSE"><img src="https://img.shields.io/badge/License-Academic_Use-F26430?style=flat-square" alt="License"/></a>
  <img src="https://img.shields.io/badge/Institusi-JTIK_UNM-red?style=flat-square" alt="JTIK UNM"/>
</p>

---

## 📖 Tentang Repositori Ini

Repositori ini adalah **otak (backend service)** dari sistem **TamuQ** — platform manajemen perpustakaan cerdas yang dikembangkan untuk Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM).

Backend ini dibangun di atas **Laravel 12** dan berfungsi sebagai pusat data, penyedia document streaming PDF inline, serta RESTful API yang dikonsumsi oleh aplikasi frontend (Vue 3). Seluruh operasi sistem — mulai dari autentikasi pengguna, pencatatan kunjungan melalui pemindaian QR Code, sirkulasi peminjaman buku, pengajuan repositori skripsi digital (E-Skripsi), hingga validasi pengajuan surat bebas pustaka — diproses dan dikelola di sini.

### 🎯 Apa yang Dilayani API Ini?

| Modul | Keterangan |
|---|---|
| 🔐 **Autentikasi** | Login, registrasi, dan manajemen sesi berbasis Laravel Sanctum |
| 📱 **Kunjungan QR** | Generate token QR satu-kali-pakai & verifikasi scan oleh staff |
| 📚 **Sirkulasi Buku** | Pencatatan peminjaman, pengembalian, kategori dinamis, dan scan user kilat |
| 🎓 **E-Skripsi Digital** | Pengajuan skripsi mahasiswa, verifikasi staff, katalog publik, dan streaming PDF inline |
| 📄 **Pengajuan Surat** | Pengajuan dan verifikasi surat bebas pustaka, donasi buku, & preview dokumen inline |
| 👤 **Manajemen User** | CRUD akun pengguna khusus role `superadmin` |
| 📊 **Dashboard Staff** | Statistik pengunjung harian, stok buku, dan status operasional perpustakaan |

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
│   ├── PengajuanSuratController.php# Pengajuan, Verifikasi & Preview Surat
│   ├── SkripsiController.php       # E-Skripsi: Pengajuan, Verifikasi & PDF Stream
│   └── AdminUserController.php     # CRUD User (superadmin only)
├── VisitController.php             # Scan QR & Riwayat Kunjungan
├── PeminjamanController.php        # Sirkulasi Peminjaman Buku & Fast Scan User
├── BukuController.php              # Manajemen Katalog & Kategori Buku
└── DashboardController.php         # Statistik & Status Operasional Perpustakaan
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
├── PengajuanSurat.php      # Tabel: pengajuan_surats | FK: user_id
└── Skripsi.php             # Tabel: skripsi     | PK: id_skripsi, FK: user_id
```

---

## 📡 Referensi Endpoint API

> **Base URL (Production):** `https://tamuq.my.id/api` (atau `https://tamuq-api.onrender.com/api`)
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

---

### 📱 Modul 3 — Kunjungan & QR Code (Auth Required)

Alur kunjungan: **Pengunjung generate QR → Staff scan QR → Kunjungan tercatat.**

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`  | `/api/qr/generate` | `pengunjung` | Generate token QR satu-kali-pakai (TTL: **30 detik**) |
| `POST` | `/api/kunjungan/scan` | `staff` | Verifikasi & catat kunjungan berdasarkan token QR |
| `GET`  | `/api/kunjungan/history` | semua | Riwayat kunjungan (staff: semua user, pengunjung: milik sendiri) |
| `GET`  | `/api/library-status` | semua | Cek status buka/tutup perpustakaan |

---

### 📚 Modul 4 — Katalog & Sirkulasi Buku (Auth Required)

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`    | `/api/buku` | semua | Mengambil seluruh daftar buku di katalog |
| `GET`    | `/api/buku/kategori` | semua | Mengambil daftar kategori buku unik/dinamis |
| `POST`   | `/api/buku` | `staff` | Menambahkan buku baru ke katalog |
| `PUT`    | `/api/buku/{id}` | `staff` | Memperbarui data buku |
| `DELETE` | `/api/buku/{id}` | `staff` | Menghapus buku dari katalog |
| `GET`    | `/api/user/buku-saya` | `pengunjung` | Melihat daftar buku yang sedang/pernah dipinjam |
| `GET`    | `/api/staff/peminjaman` | `staff` | Daftar peminjaman aktif + riwayat pengembalian |
| `POST`   | `/api/staff/peminjaman` | `staff` | Mencatat transaksi peminjaman buku baru |
| `POST`   | `/api/staff/peminjaman/{id}/return` | `staff` | Mencatat pengembalian buku |
| `GET`    | `/api/staff/users-list` | `staff` | Daftar user untuk keperluan form peminjaman |
| `POST`   | `/api/staff/peminjaman/scan-user` | `staff` | Fast user scan via QR token untuk peminjaman |

**Request Body `POST /api/staff/peminjaman/scan-user`:**
```json
{
  "qr_code": "Kj3xLmNpQ7rStUvWxYzAb..."
}
```

**Response Sukses (`200 OK`):**
```json
{
  "status": "success",
  "data": {
    "id_user": 5,
    "nama_lengkap": "Muhammad Ikram Muslimin",
    "nomor_induk": "210209502028",
    "prodi": "PTIK"
  }
}
```

---

### 📄 Modul 5 — Pengajuan Surat & Document Stream (Auth Required)

Mendukung dua jenis pengajuan: **Bebas Perpustakaan** dan **Donasi Buku**. Dilengkapi endpoint streaming PDF & image inline serta fitur pembaruan balasan/pesan tanpa membatalkan proses verifikasi awal.

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`   | `/api/pengajuan` | semua | Daftar pengajuan (staff: semua, pengunjung: milik sendiri) |
| `POST`  | `/api/pengajuan` | `pengunjung` | Membuat pengajuan baru (upload `file_pendukung`) |
| `POST`  | `/api/pengajuan/{id}/verify` | `staff` | Verifikasi pengajuan (`Diterima`/`Ditolak`) + upload `file_balasan` |
| `PATCH` | `/api/pengajuan/{id}/update-pesan` | `staff` | Mengubah pesan/keterangan verifikasi pengajuan |
| `POST`  | `/api/pengajuan/{id}/update-balasan` | `staff` | Mengubah file balasan & pesan verifikasi pengajuan |
| `GET`   | `/api/download-pendukung/{filename}` | semua | Unduh file dokumen pendukung |
| `GET`   | `/api/download-balasan/{filename}` | semua | Unduh file surat balasan dari staff |
| `GET`   | `/api/preview-pendukung/{filename}` | semua | Stream/preview inline dokumen pendukung (PDF/Image) |
| `GET`   | `/api/preview-balasan/{filename}` | semua | Stream/preview inline surat balasan (PDF/Image) |

---

### 🎓 Modul 8 — E-Skripsi & Repositori Digital (Auth Required)

Modul ini dikhususkan untuk repositori karya ilmiah/skripsi digital mahasiswa JTIK UNM, mendukung pengunggahan skripsi penuh + lembar pengesahan (preview), katalog publik skripsi disetujui, dan streaming PDF inline.

| Method | Endpoint | Role | Keterangan |
|--------|----------|------|------------|
| `GET`    | `/api/skripsi` | semua | Mengambil katalog skripsi (pengunjung: disetujui saja; staff: semua/filter `status`) |
| `GET`    | `/api/skripsi/saya` | `pengunjung` | Mengambil riwayat pengajuan skripsi milik user yang sedang login |
| `POST`   | `/api/skripsi` | `pengunjung` | Mengajukan skripsi baru (`file_skripsi`, `file_preview_skripsi`, `foto`) |
| `GET`    | `/api/skripsi/{id}/preview` | semua | Stream/preview inline file skripsi utama (PDF) |
| `GET`    | `/api/skripsi/{id}/download` | semua | Mengunduh file skripsi utama (PDF) |
| `GET`    | `/api/skripsi/{id}/preview-file` | semua | Stream/preview inline file lembar pengesahan/preview (PDF) |
| `GET`    | `/api/skripsi/{id}/download-file` | semua | Mengunduh file lembar pengesahan/preview (PDF) |
| `POST`   | `/api/staff/skripsi` | `staff` | Menambahkan data skripsi baru langsung oleh staff (status auto `disetujui`) |
| `POST`   | `/api/staff/skripsi/{id}` | `staff` | Memperbarui data skripsi yang sudah ada |
| `DELETE` | `/api/staff/skripsi/{id}` | `staff` | Menghapus data skripsi dari repositori |
| `POST`   | `/api/staff/skripsi/{id}/verify` | `staff` | Verifikasi status skripsi (`disetujui` / `ditolak` + `keterangan`) |

#### `POST /api/skripsi` (`multipart/form-data`)
```
judul                : string (wajib)
abstrak              : string (wajib)
tahun_selesai        : integer (wajib, contoh: 2026)
foto                 : image file (opsional, max 5MB)
file_skripsi         : pdf file (wajib, max 20MB)
file_preview_skripsi : pdf file (opsional, max 10MB)
```

#### `POST /api/staff/skripsi/{id}/verify`
```json
{
  "status": "disetujui",
  "keterangan": "Skripsi Diterima, Harap Cek Skripsi Di Katalog Skripsi"
}
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

Backend ini telah dikonfigurasi secara penuh untuk deployment production di lingkungan cloud via Docker & Render.com. Terdapat berkas `Dockerfile` dan `render.yaml` di root backend.

---

## ⚖️ Hak Cipta

<div align="center">

```
╔══════════════════════════════════════════════════════════════════════╗
║             HAK KEKAYAAN INTELEKTUAL (HKI) — TERDAFTAR               ║
╠══════════════════════════════════════════════════════════════════════╣
║  Nama Program  : TamuQ - Smart Library Guestbook &                  ║
║                  Management System                                   ║
║  Jenis Ciptaan : Program Komputer                                    ║
║  Institusi     : Jurusan Teknik Informatika dan Komputer (JTIK)     ║
║                  Universitas Negeri Makassar (UNM)                  ║
║  Lembaga HKI   : DJKI — Direktorat Jenderal Kekayaan Intelektual   ║
║                  Kementerian Hukum dan HAM Republik Indonesia        ║
║  Status        : ✅ Terdaftar Resmi & Dilindungi Undang-Undang      ║
╚══════════════════════════════════════════════════════════════════╝
```

</div>

Seluruh kode sumber backend service ini merupakan bagian tak terpisahkan dari **Sistem TamuQ**, karya cipta yang dilindungi oleh **Undang-Undang Republik Indonesia Nomor 28 Tahun 2014 tentang Hak Cipta** dan terdaftar secara resmi di Direktorat Jenderal Kekayaan Intelektual (DJKI), Kementerian Hukum dan HAM Republik Indonesia melalui **2 (dua) Surat Pencatatan Ciptaan**:

| Sertifikat 1 | Sertifikat 2 |
|---|---|
| **Judul Ciptaan:**<br/>TamuQ: Sistem Buku Tamu Digital dan Manajemen Layanan Perpustakaan JTIK UNM | **Judul Ciptaan:**<br/>Logic System Website TamuQ: Buku Tamu Perpustakaan JTIK UNM |
| **Jenis Ciptaan:** Program Komputer | **Jenis Ciptaan:** Program Komputer |
| **Nomor Pencatatan:** `001348850` | **Nomor Pencatatan:** `001348857` |
| **Permohonan:** EC002026114925 (15 Juli 2026) | **Permohonan:** EC002026114926 (15 Juli 2026) |
| **Pencipta (Sesuai Urutan Resmi):**<br/>1. Prof. Dr. H. Ruslan, M.Pd.<br/>2. Muhammad Ikram Muslimin<br/>3. Ayu Tri Wardani S.Pd., M.Pd. | **Pencipta (Sesuai Urutan Resmi):**<br/>1. Ayu Tri Wardani S.Pd., M.Pd.<br/>2. Muhammad Ikram Muslimin<br/>3. Prof. Dr. H. Ruslan, M.Pd. |
| **Pemegang Hak Cipta:**<br/>Universitas Negeri Makassar | **Pemegang Hak Cipta:**<br/>Universitas Negeri Makassar |
| **Pengumuman Pertama:**<br/>15 Juli 2026, di Kota Makassar | **Pengumuman Pertama:**<br/>15 Juli 2026, di Kota Makassar |

---

## 👨‍💻 Pengembang

Backend service ini **dibangun dan dikembangkan oleh [Muhammad Ikram Muslimin](https://github.com/MuhammadIkramM)** sebagai pengembang utama (lead developer) dalam rangka penyusunan **tugas akhir / skripsi** di JTIK UNM. Muhammad Ikram Muslimin memegang peranan penuh atas perancangan arsitektur API, skema basis data, logika bisnis, dan integrasi document stream, serta tercatat secara resmi sebagai salah satu **Pencipta** dalam kedua Surat Pencatatan Ciptaan DJKI di atas.

Hak Cipta resmi dipegang oleh **Universitas Negeri Makassar**. Lisensi penggunaan penuh mengacu pada berkas [`LICENSE`](./LICENSE).

---

<p align="center">
  Dikembangkan oleh Muhammad Ikram Muslimin.
  <br>
  <em>© 2024–2026 TamuQ System. All Rights Reserved.</em>
</p>
