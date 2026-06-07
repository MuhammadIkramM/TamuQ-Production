<div align="center">

<img src="tamuq-frontend/public/tamuqicon.png" alt="TamuQ Logo" width="110" style="border-radius: 22px; margin-bottom: 16px;" />

# TamuQ — Smart Library Guestbook & Management System

### *Ekosistem Digital Terintegrasi untuk Layanan Perpustakaan JTIK UNM*

<br/>

[![Top Language](https://img.shields.io/github/languages/top/MuhammadIkramM/TamuQ-Production?style=for-the-badge&color=4FC08D)](https://github.com/MuhammadIkramM/TamuQ-Production)
[![Repo Size](https://img.shields.io/github/repo-size/MuhammadIkramM/TamuQ-Production?style=for-the-badge&color=646CFF)](https://github.com/MuhammadIkramM/TamuQ-Production)
[![Last Commit](https://img.shields.io/github/last-commit/MuhammadIkramM/TamuQ-Production?style=for-the-badge&color=FF2D20)](https://github.com/MuhammadIkramM/TamuQ-Production/commits)
[![HKI Terdaftar](https://img.shields.io/badge/HKI-Terdaftar_DJKI-00555A?style=for-the-badge&logo=gov.uk&logoColor=white)](#️-hak-kekayaan-intelektual--lisensi)
[![License](https://img.shields.io/badge/License-Academic_Use_Only-F26430?style=for-the-badge)](./LICENSE)
[![Status](https://img.shields.io/badge/Status-Production_Ready-brightgreen?style=for-the-badge)](https://github.com/MuhammadIkramM/TamuQ-Production)

<br/>

> **"Dari buku tamu kertas ke ekosistem digital terintegrasi — TamuQ hadir untuk masa depan perpustakaan kampus."**

<br/>

[📦 Frontend](#-tamuq-frontend--antarmuka-pengguna) · [⚙️ Backend](#️-tamuq-backend--layanan-api) · [🏗️ Arsitektur](#️-arsitektur-sistem) · [🔒 Keamanan](#-arsitektur-keamanan) · [⚖️ Lisensi](#️-hak-kekayaan-intelektual--lisensi)

</div>

---

## 📖 Tentang Repositori Ini

Repositori ini adalah **monorepo** dari platform **TamuQ** — sistem manajemen perpustakaan cerdas berbasis web yang dikembangkan khusus untuk **Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM)**.

TamuQ lahir dari kebutuhan transformasi digital di lingkungan perpustakaan kampus — menggantikan proses manual yang lambat dan rawan kesalahan dengan solusi digital yang efisien, andal, dan modern.

Repositori ini mengandung dua layanan utama yang bekerja bersama dalam satu ekosistem terintegrasi:

| Layanan | Teknologi | Peran |
|---|---|---|
| 📦 **`tamuq-frontend`** | Vue.js 3, Vite, Tailwind CSS | Antarmuka Pengguna & Staff (SPA) |
| ⚙️ **`tamuq-backend`** | Laravel, Sanctum, PostgreSQL | Pusat Data & RESTful API |

---

## 🏗️ Arsitektur Sistem

TamuQ mengimplementasikan pola arsitektur **Client-Server** dalam satu monorepo, di mana frontend dan backend beroperasi sebagai dua layanan yang terpisah namun terhubung erat melalui antarmuka REST API.

```
╔══════════════════════════════════════════════════════════════════════╗
║                     TamuQ — Ekosistem Monorepo                      ║
╠══════════════════════════════════════════════════════════════════════╣
║                                                                      ║
║   ┌─────────────────────────┐       ┌──────────────────────────┐    ║
║   │    tamuq-frontend        │       │     tamuq-backend         │    ║
║   │  ┌────────────────────┐ │       │  ┌──────────────────────┐ │    ║
║   │  │   Vue.js 3 (SPA)   │ │       │  │   Laravel REST API   │ │    ║
║   │  │   Vite + Tailwind  │ │◄─────►│  │   Sanctum Auth       │ │    ║
║   │  │   Pinia + Router   │ │  API  │  │   PostgreSQL Database │ │    ║
║   │  └────────────────────┘ │ calls │  └──────────────────────┘ │    ║
║   │                         │       │                            │    ║
║   │  👤 Pengunjung/Mhs       │       │  🗄️ Data & Business Logic  │    ║
║   │  🛠️ Staff Perpustakaan   │       │  🔐 Auth & Authorization  │    ║
║   └─────────────────────────┘       └──────────────────────────┘    ║
║                                                                      ║
╚══════════════════════════════════════════════════════════════════════╝
```

### Alur Komunikasi

1. **Pengguna** (Pengunjung / Staff) berinteraksi dengan antarmuka berbasis **Vue.js** melalui browser.
2. Frontend mengirimkan **request HTTP** ke endpoint API yang disediakan oleh backend Laravel.
3. Backend memproses logika bisnis, berkomunikasi dengan **database**, lalu mengembalikan respons JSON.
4. Frontend merender data secara reaktif dan menampilkan hasilnya ke pengguna secara real-time.

### Modul Fungsional Utama

| Modul | Deskripsi |
|---|---|
| 🔐 **Autentikasi & Otorisasi** | Sistem login berbasis peran (pengunjung, staff, superadmin) |
| 📱 **Absensi QR Code** | Generate & scan QR Code untuk pencatatan kunjungan perpustakaan |
| 📚 **Sirkulasi Buku** | Manajemen peminjaman, pengembalian, dan katalog koleksi |
| 📄 **Layanan Surat** | Pengajuan & verifikasi surat bebas pustaka dan donasi buku secara digital |
| 📊 **Dashboard Analitik** | Visualisasi statistik pengunjung, demografi, dan status operasional |
| 👤 **Manajemen Akun** | Pengelolaan akun pengguna oleh administrator sistem |

---

## 📁 Struktur Repositori

```
TamuQ-Production/                    ← Root Monorepo (Anda di sini)
│
├── 📦 tamuq-frontend/               ← Antarmuka Pengguna & Staff (Vue.js 3)
│   ├── src/
│   │   ├── views/                   # Halaman-halaman utama aplikasi
│   │   ├── components/              # Komponen Vue yang dapat digunakan ulang
│   │   ├── stores/                  # State management (Pinia)
│   │   └── router/                  # Konfigurasi routing & navigation guard
│   ├── public/
│   │   └── wasm/                    # WebAssembly module (QR decoder)
│   └── README.md                    # ← Dokumentasi teknis frontend
│
├── ⚙️ tamuq-backend/                ← Layanan API & Database (Laravel)
│   ├── app/
│   │   ├── Http/Controllers/        # Controller API per modul
│   │   └── Models/                  # Definisi model & relasi database
│   ├── routes/                      # Definisi routing API
│   ├── database/migrations/         # Skema dan migrasi database
│   ├── Dockerfile                   # Konfigurasi containerisasi Docker
│   ├── render.yaml                  # Konfigurasi deployment cloud (Render)
│   └── README.md                    # ← Dokumentasi teknis & referensi API
│
└── README.md                        ← Dokumen ini (High-Level Overview)
```

---

## 📦 `tamuq-frontend` — Antarmuka Pengguna

Direktori `tamuq-frontend` memuat seluruh kode sumber antarmuka berbasis **Vue.js 3** yang berjalan sepenuhnya di browser sebagai Single Page Application (SPA).

**Teknologi Utama:** Vue.js 3 · Vite · Tailwind CSS 4 · Pinia · Vue Router · Axios · Chart.js · WebAssembly

Antarmuka ini melayani dua jenis peran pengguna:
- **Pengunjung / Mahasiswa** — Panel personal untuk QR Code, riwayat kunjungan, layanan buku, dan pengajuan surat.
- **Staff / Admin** — Panel operasional untuk scan QR, manajemen inventaris, validasi pengajuan, dan monitoring analitik.

> 📖 **Untuk panduan instalasi lokal, konfigurasi environment, dan dokumentasi komponen secara lengkap, silakan baca:**
> **[`tamuq-frontend/README.md`](./tamuq-frontend/README.md)**

---

## ⚙️ `tamuq-backend` — Layanan API

Direktori `tamuq-backend` memuat seluruh kode sumber layanan RESTful API yang dibangun di atas **Laravel** dengan database **PostgreSQL**. Layanan ini bertindak sebagai pusat data dan otak logika bisnis sistem TamuQ.

**Teknologi Utama:** Laravel · Laravel Sanctum · PostgreSQL · Docker · Render (Cloud Deployment)

Backend menyediakan endpoint API yang terorganisir dalam modul-modul berikut:
- Autentikasi & Manajemen Sesi
- Manajemen QR Token & Kunjungan
- Katalog & Sirkulasi Buku
- Pengajuan & Verifikasi Surat
- Dashboard Statistik & Status Perpustakaan
- Manajemen Akun (Superadmin)

> 📖 **Untuk referensi endpoint API, panduan instalasi lokal, dan konfigurasi deployment Docker/Render, silakan baca:**
> **[`tamuq-backend/README.md`](./tamuq-backend/README.md)**

---

## 🔒 Arsitektur Keamanan

Sistem TamuQ dibangun dengan menerapkan praktik keamanan standar industri di setiap lapisan aplikasi:

| Mekanisme | Implementasi |
|---|---|
| 🔑 **Autentikasi Berbasis Token** | Sesi pengguna dikelola menggunakan mekanisme cookie HttpOnly yang aman, mencegah akses skrip pihak ketiga terhadap kredensial sesi |
| 🛡️ **Role-Based Access Control** | Setiap endpoint API dan halaman antarmuka dilindungi oleh middleware otorisasi berbasis peran (pengunjung, staff, superadmin) |
| 🌐 **Proteksi CORS** | Kebijakan Cross-Origin Resource Sharing dikonfigurasi secara ketat di sisi server untuk membatasi origin yang diizinkan mengakses API |
| 🔏 **Pemisahan Environment** | Seluruh konfigurasi sensitif (kunci aplikasi, kredensial layanan) dikelola melalui variabel lingkungan (`.env`) dan **tidak pernah di-commit** ke dalam repositori |
| ⚡ **Rate Limiting** | Endpoint autentikasi dilindungi oleh pembatasan jumlah percobaan per interval waktu untuk mencegah serangan brute-force |
| ✅ **Validasi Input** | Setiap data yang masuk ke sistem divalidasi dan disanitasi di sisi server sebelum diproses lebih lanjut |
| 🔒 **Transaksi Database** | Operasi kritis menggunakan mekanisme transaksi atomik dengan penguncian baris (row lock) untuk menjaga integritas data secara konkuren |

> **Catatan Keamanan:** Repositori ini tidak mengandung kredensial, kunci API, alamat IP server produksi, atau konfigurasi lingkungan aktif dalam bentuk apapun. Semua informasi sensitif dikelola secara terpisah di luar repositori.

---

## ⚖️ Hak Kekayaan Intelektual & Lisensi

<div align="center">

```
╔══════════════════════════════════════════════════════════════════════╗
║                HAK KEKAYAAN INTELEKTUAL — TERDAFTAR                 ║
╠══════════════════════════════════════════════════════════════════════╣
║  Nama Program  : TamuQ - Smart Library Guestbook &                  ║
║                  Management System                                   ║
║  Kategori      : Program Komputer                                    ║
║  Pengembang    : Muhammad Ikram Muslimin                             ║
║  Instansi      : Jurusan Teknik Informatika dan Komputer (JTIK)     ║
║                  Universitas Negeri Makassar (UNM)                  ║
║  Lembaga HKI   : DJKI — Direktorat Jenderal Kekayaan Intelektual   ║
║                  Kementerian Hukum dan HAM Republik Indonesia        ║
║  Status        : ✅ Terdaftar & Dilindungi Undang-Undang            ║
╚══════════════════════════════════════════════════════════════════════╝
```

</div>

Seluruh kode sumber, desain antarmuka, arsitektur sistem, dan aset digital yang terdapat dalam repositori ini merupakan **hak kekayaan intelektual** milik Muhammad Ikram Muslimin yang telah **terdaftar secara resmi sebagai Program Komputer** di Direktorat Jenderal Kekayaan Intelektual (DJKI), Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia.

Sistem ini dikembangkan secara eksklusif untuk kepentingan akademik dan operasional **Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM)**.

> **⚠️ Perhatian Hukum**
>
> Segala bentuk **reproduksi, distribusi, modifikasi, penggunaan komersial**, atau **penggunaan di luar konteks yang diizinkan** atas seluruh atau sebagian dari sistem ini — tanpa izin tertulis dari pemilik hak cipta — adalah **dilarang keras** dan dapat dikenakan sanksi hukum sesuai peraturan perundang-undangan yang berlaku di Republik Indonesia.

---

<div align="center">

[![Universitas Negeri Makassar](https://img.shields.io/badge/Universitas-Negeri_Makassar-00555A?style=flat-square)](https://unm.ac.id/)
[![JTIK UNM](https://img.shields.io/badge/Jurusan-JTIK_UNM-F26430?style=flat-square)](https://ft.unm.ac.id/)
[![DJKI Kemenkumham](https://img.shields.io/badge/HKI-DJKI_Kemenkumham_RI-003087?style=flat-square)](https://dgip.go.id/)

<br/>

**Scan. Track. Manage.**

*TamuQ — Karena setiap kunjungan ke perpustakaan itu berharga.*

<br/>

*© 2024–2026 TamuQ System. Dikembangkan oleh Muhammad Ikram Muslimin. All Rights Reserved.*

</div>
