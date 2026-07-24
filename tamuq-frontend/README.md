<div align="center">

<img src="public/tamuqicon.png" alt="TamuQ Logo" width="120" style="border-radius: 24px; margin-bottom: 16px;" />

# TamuQ — Smart Library Guestbook & Management System

### *Elevating Campus Library Experience through Digital Innovation*

<br/>

[![Vue 3](https://img.shields.io/badge/Vue-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Laravel API](https://img.shields.io/badge/Backend-Laravel_API-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Pinia](https://img.shields.io/badge/State-Pinia-F7D336?style=for-the-badge&logo=pinia&logoColor=black)](https://pinia.vuejs.org/)
[![WebAssembly](https://img.shields.io/badge/QR_Decode-WebAssembly-6543C4?style=for-the-badge&logo=webassembly&logoColor=white)](https://webassembly.org/)
[![HKI Registered](https://img.shields.io/badge/HKI-Terdaftar_DJKI-00555A?style=for-the-badge&logo=gov.uk&logoColor=white)](#️-hak-cipta)
[![License](https://img.shields.io/badge/License-Academic_Use-F26430?style=for-the-badge)](./LICENSE)

<br/>

> **"Dari buku tamu kertas ke sistem digital terintegrasi — TamuQ hadir untuk masa depan perpustakaan kampus."**

<br/>

[🚀 Mulai Sekarang](#-quick-start--instalasi) · [✨ Fitur Lengkap](#-fitur-unggulan) · [🏗 Arsitektur](#️-arsitektur-teknologi-tech-stack) · [⚖️ Hak Cipta & Lisensi](#️-hak-cipta)

</div>

---

## 🎥 Demo Visual

<div align="center">

<a href="src/assets/web_demo.mp4" title="Klik untuk melihat demo TamuQ">
  <img src="src/assets/thumbnailvid.png" alt="Preview Demo TamuQ — Klik untuk memutar" width="85%" style="border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); border: 3px solid #00555A;" />
  <br/>
  <sub>▶ &nbsp;<strong>Klik thumbnail di atas</strong> untuk melihat video demo lengkap · <code>src/assets/web_demo.mp4</code></sub>
</a>

<br/><br/>

Or play directly in browser:

```html
<video controls width="100%" style="border-radius:12px;">
  <source src="src/assets/web_demo.mp4" type="video/mp4" />
  Browser Anda tidak mendukung pemutaran video.
</video>
```

</div>

---

## 📖 Tentang TamuQ

**TamuQ** adalah **Sistem Informasi Perpustakaan Pintar** yang dikembangkan khusus untuk Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM). Sistem ini lahir dari kebutuhan nyata akan transformasi digital di lingkungan perpustakaan kampus — menggantikan proses manual yang lambat, rawan kesalahan, dan tidak efisien.

<br/>

### 🔍 Masalah yang Diselesaikan

| ❌ Kondisi Konvensional | ✅ Solusi dengan TamuQ |
|---|---|
| Buku tamu fisik yang mudah rusak dan susah diaudit | Check-in digital via QR Code, tercatat otomatis |
| Tidak ada data kunjungan real-time untuk analisis | Dashboard analitik dengan grafik tren kunjungan |
| Proses peminjaman buku manual dan lambat | Manajemen sirkulasi buku digital, fast scan & real-time |
| Repositori karya ilmiah/skripsi belum terintegrasi | Modul E-Skripsi Digital & document streaming PDF inline |
| Pengajuan surat bebas pustaka berbasis kertas | Pengajuan & validasi surat 100% paperless online |
| Tidak ada visibilitas data demografi pengunjung | Visualisasi demografis per program studi (PTIK & TEKOM) |
| Akses monitoring hanya saat petugas hadir | Kendali status operasional perpustakaan dari mana saja |

<br/>

> **TamuQ bukan sekadar aplikasi pencatat tamu** — ini adalah ekosistem manajemen perpustakaan modern yang menghubungkan mahasiswa, staf, repositori skripsi digital, dan data secara mulus dalam satu platform terintegrasi.

---

## ✨ Fitur Unggulan

### 👥 Fitur Pengunjung / Mahasiswa (Role: `pengunjung`)

Setiap mahasiswa yang terdaftar mendapatkan akses ke panel user yang personal dan intuitif:

| Fitur | Deskripsi | Komponen Utama |
|---|---|---|
| 📊 **Dashboard Interaktif** | Ringkasan status kunjungan, informasi perpustakaan, dan akses cepat layanan | `DashboardUser.vue` |
| 🔲 **QR Code Generator** | Generate QR Code unik milik sendiri untuk proses check-in kilat tanpa antri | `QRCodeUser.vue` |
| 📚 **Layanan Buku** | Lihat katalog koleksi buku, filter kategori, dan cek riwayat peminjaman | `LayananBukuUser.vue` |
| 🎓 **E-Skripsi Digital** | Pengajuan skripsi digital (upload PDF skripsi + lembar pengesahan), katalog publik, & streaming PDF inline | `ESkripsiUser.vue` |
| 🕒 **Riwayat Kunjungan** | Rekam jejak lengkap semua kunjungan ke perpustakaan beserta timestamp akurat | `RiwayatKunjunganUser.vue` |
| 📦 **Pengajuan Donasi Buku** | Formulir pengajuan donasi buku digital lengkap dengan tracking status | `PengajuanDonasiBukuUser.vue` |
| 📄 **Surat Bebas Pustaka Online** | Ajukan surat keterangan bebas pustaka secara digital beserta preview balasan | `PengajuanSuratBebasUser.vue` |
| ⚙️ **Pengaturan Profil** | Kelola data pribadi, foto profil, dan preferensi akun secara mandiri | `PengaturanUser.vue` |

<br/>

### 🛠️ Fitur Administrator / Staff Perpustakaan (Role: `staff`, `admin`, `superadmin`)

Panel staff dirancang untuk efisiensi operasional dan insight berbasis data:

| Fitur | Deskripsi | Komponen Utama |
|---|---|---|
| 📈 **Dashboard Analitik** | Overview real-time: total koleksi, pengunjung hari ini, peminjaman aktif & kontrol status perpustakaan | `DashboardStaff.vue` |
| 📉 **Grafik Tren Kunjungan** | Line chart interaktif via `LineChart.vue` dengan filter Hari Ini / Minggu Ini / Bulan Ini / Tanggal Kustom | `LineChart.vue` |
| 🏫 **Demografis Prodi** | Bar chart `BarGraph.vue` visualisasi distribusi pengunjung berdasarkan program studi | `BarGraph.vue` |
| 📡 **Scan QR Code Pengunjung** | Scan QR Code mahasiswa langsung dari browser menggunakan **WebAssembly** (`zxing_reader.wasm`) | `ScanQR.vue` |
| 📖 **Manajemen Inventaris Buku** | CRUD lengkap koleksi buku, kategori dinamis, dan pencatatan transaksi peminjaman (support fast QR scan user) | `ManajemenBuku.vue` |
| 📜 **Manajemen & Verifikasi Skripsi** | Review pengajuan skripsi mahasiswa, verifikasi status (disetujui/ditolak), edit data, & modal detail skripsi | `ManajemenSkripsi.vue` |
| 📋 **Validasi Surat Bebas Pustaka** | Review pengajuan surat, upload surat balasan, edit pesan/balasan verifikasi, & preview dokumen inline | `PengajuanSurat.vue` |
| 👤 **Manajemen Akun** | Kelola seluruh akun pengguna — khusus untuk role `admin` dan `superadmin` | `ManajemenAkun.vue` |
| 🔐 **Kendali Status Perpustakaan** | Buka/tutup akses masuk perpustakaan secara real-time langsung dari dashboard | `DashboardStaff.vue` |
| ⚙️ **Pengaturan Staff** | Konfigurasi profil dan preferensi akun petugas | `PengaturanStaff.vue` |

<br/>

---

## 🗺️ Peta Alur Sistem (User Flow)

```
[ Landing Page (/) ]
       │
       ├── [ Login (/login) ] ──────────────────────────────────┐
       │                                                         │
       └── [ Register (/register) ]                             │
                                                                  ▼
                                                     ┌────────────────────────┐
                                                     │   Auth Guard (Pinia)   │
                                                     │   useAuthStore         │
                                                     └─────────┬──────────────┘
                                                               │
                       ┌──────────────────────────────────────┴──────────────────────────────────┐
                       │                                                                          │
              Role: pengunjung                                                          Role: staff/admin/superadmin
                       │                                                                          │
                       ▼                                                                          ▼
         ┌─────────────────────────┐                                              ┌──────────────────────────────────┐
         │   Panel User (/user/)   │                                              │   Panel Staff (/staff/)          │
         ├─────────────────────────┤                                              ├──────────────────────────────────┤
         │  • dashboard            │                                              │  • dashboard (semua role)        │
         │  • qr                   │                                              │  • scan-qr (staff only)          │
         │  • riwayat              │                                              │  • riwayat-pengunjung            │
         │  • layanan-buku         │                                              │  • manajemen-buku                │
         │  • e-skripsi            │                                              │  • manajemen-skripsi             │
         │  • surat-bebas          │                                              │  • pengajuan-surat               │
         │  • donasi               │                                              │  • manajemen-akun (admin only)   │
         │  • pengaturan           │                                              │  • pengaturan                    │
         └─────────────────────────┘                                              └──────────────────────────────────┘
```

---

## 🏗️ Arsitektur Teknologi (Tech Stack)

<div align="center">

| Lapisan | Teknologi | Versi | Peran |
|---|---|---|---|
| **Core Framework** | [Vue.js](https://vuejs.org/) | `^3.5.24` | SFC, Composition API, `<script setup>` |
| **Build Tool** | [Vite](https://vitejs.dev/) | `^7.2.2` | Bundler ultra-cepat untuk development & production |
| **Styling** | [Tailwind CSS](https://tailwindcss.com/) | `^4.1.17` | Utility-first CSS untuk desain responsif modern |
| **State Management** | [Pinia](https://pinia.vuejs.org/) | `^3.0.4` | Auth state, session token, role management |
| **Routing** | [Vue Router](https://router.vuejs.org/) | `^4.6.3` | SPA routing dengan navigation guard berbasis peran |
| **HTTP Client** | [Axios](https://axios-http.com/) | `^1.13.2` | Komunikasi REST API ke backend Laravel |
| **Charting** | [Chart.js](https://www.chartjs.org/) + [vue-chartjs](https://vue-chartjs.org/) | `^4.5.1` | Visualisasi data analitik (line & bar chart) |
| **QR Generator** | [qrcode.vue](https://github.com/scopewu/qrcode.vue) | `^3.6.0` | Generate QR Code unik per user |
| **QR Scanner** | [vue-qrcode-reader](https://github.com/gruhn/vue-qrcode-reader) + **WebAssembly** | `^5.7.3` | Scan QR Code di browser via kamera — powered by ZXing WASM |
| **Notifikasi** | [SweetAlert2](https://sweetalert2.github.io/) | `^11.26.17` | Dialog konfirmasi & notifikasi yang elegan |
| **Export Data** | [SheetJS (xlsx)](https://sheetjs.com/) + [file-saver](https://github.com/eligrey/FileSaver.js) | `^0.18.5` | Export data ke format Excel |
| **Icons** | [@heroicons/vue](https://heroicons.com/) | `^2.2.0` | Ikon SVG berkualitas tinggi konsisten di seluruh UI |

</div>

---

## 🚀 Quick Start — Instalasi

### Prasyarat

Pastikan sistem Anda sudah terpasang:
- **Node.js** `>= 18.x`
- **npm** `>= 9.x`
- **Git**
- Backend **TamuQ API** (Laravel) yang sudah berjalan

```bash
# 1. Clone repositori
git clone https://github.com/MuhammadIkramM/TamuQ-Production.git
cd TamuQ-Production/tamuq-frontend

# 2. Instal dependensi
npm install

# 3. Jalankan mode development
npm run dev
```

---

### Struktur Direktori Utama

```
tamuq-frontend/
├── public/
│   ├── tamuqicon.png          # Favicon & logo utama aplikasi
│   ├── og-image.png           # Open Graph image untuk SEO/sosmed
│   └── wasm/
│       └── zxing_reader.wasm  # WebAssembly module untuk QR Code decoding
├── src/
│   ├── components/
│   │   ├── DetailSkripsiModal.vue  # Modal detail skripsi (preview PDF, abstrak & meta)
│   │   ├── GlobalProgressBar.vue   # Top progress bar global untuk async request
│   │   └── charts/
│   │       ├── BarGraph.vue        # Komponen bar chart (demografis prodi)
│   │       └── LineChart.vue       # Komponen line chart (tren kunjungan)
│   ├── layouts/
│   │   ├── SideBarStaff.vue        # Layout sidebar untuk panel staff
│   │   └── BarUser.vue             # Layout navbar untuk panel user
│   ├── router/
│   │   └── index.js                # Konfigurasi routing & navigation guard
│   ├── stores/
│   │   └── auth.js                 # Pinia store: auth, session, role management
│   ├── views/
│   │   ├── LandingPage.vue         # Halaman utama / beranda publik
│   │   ├── Auth/
│   │   │   ├── LoginView.vue
│   │   │   └── RegisterView.vue
│   │   ├── Staff/
│   │   │   ├── DashboardStaff.vue    # Dashboard analitik staff
│   │   │   ├── ScanQR.vue            # Scanner QR Code via kamera
│   │   │   ├── RiwayatPengunjung.vue # Histori kunjungan seluruh user
│   │   │   ├── ManajemenBuku.vue     # CRUD inventaris & peminjaman buku
│   │   │   ├── ManajemenSkripsi.vue  # Verifikasi & kelola repositori skripsi
│   │   │   ├── PengajuanSurat.vue    # Validasi surat bebas pustaka
│   │   │   ├── ManajemenAkun.vue     # Manajemen akun (admin only)
│   │   │   └── PengaturanStaff.vue   # Pengaturan profil staff
│   │   └── User/
│   │       ├── DashboardUser.vue     # Dashboard personal mahasiswa
│   │       ├── QRCodeUser.vue        # Tampil & generate QR Code
│   │       ├── RiwayatKunjunganUser.vue  # Riwayat kunjungan pribadi
│   │       ├── LayananBukuUser.vue   # Katalog & layanan buku
│   │       ├── ESkripsiUser.vue      # Katalog E-Skripsi & pengajuan mahasiswa
│   │       ├── PengajuanSuratBebasUser.vue # Pengajuan surat bebas pustaka
│   │       ├── PengajuanDonasiBukuUser.vue # Pengajuan donasi buku
│   │       └── PengaturanUser.vue    # Pengaturan profil user
│   ├── api.js                      # Instance Axios dengan base URL & interceptor
│   ├── main.js                     # Entry point aplikasi Vue
│   └── style.css                   # Global CSS & Tailwind base imports
├── LICENSE                         # ← Lisensi & HKI Frontend
└── package.json                    # Dependensi & scripts npm
```

---

## 🤝 Kontribusi

Proyek ini dikembangkan oleh **Muhammad Ikram Muslimin** dalam rangka penyusunan skripsi di lingkungan **Jurusan Teknik Informatika dan Komputer (JTIK), Universitas Negeri Makassar (UNM)** dan saat ini **tidak membuka kontribusi eksternal secara umum**. Jika Anda adalah civitas akademika JTIK UNM atau memiliki kepentingan resmi, silakan hubungi pengembang melalui repositori GitHub resmi.

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

Seluruh kode sumber, desain antarmuka, alur sistem, dan aset visual yang terdapat dalam repositori ini dilindungi oleh **Undang-Undang Republik Indonesia Nomor 28 Tahun 2014 tentang Hak Cipta** dan terdaftar secara resmi di Direktorat Jenderal Kekayaan Intelektual (DJKI), Kementerian Hukum dan HAM Republik Indonesia melalui **2 (dua) Surat Pencatatan Ciptaan**:

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

Antarmuka pengguna (frontend) TamuQ **dibangun dan dikembangkan oleh [Muhammad Ikram Muslimin](https://github.com/MuhammadIkramM)** sebagai pengembang utama (lead developer) dalam rangka penelitian skripsi di JTIK UNM. Muhammad Ikram Muslimin tercatat secara resmi sebagai salah satu **Pencipta** dalam kedua Surat Pencatatan Ciptaan di atas, dengan Pemegang Hak Cipta resmi **Universitas Negeri Makassar**. Detail lisensi dapat dibaca pada berkas [`LICENSE`](./LICENSE).

---

<div align="center">

[![Universitas Negeri Makassar](https://img.shields.io/badge/Universitas-Negeri_Makassar-00555A?style=flat-square)](https://unm.ac.id/)
[![JTIK UNM](https://img.shields.io/badge/Jurusan-JTIK_UNM-F26430?style=flat-square)](https://ft.unm.ac.id/)
[![DJKI Kemenkumham](https://img.shields.io/badge/HKI-DJKI_Kemenkumham_RI-003087?style=flat-square)](https://dgip.go.id/)

<br/>

**Scan. Track. Manage.**

*TamuQ — Karena setiap kunjungan ke perpustakaan itu berharga.*

</div>
