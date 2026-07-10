<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_user')->autoIncrement();
            $table->string('nama_lengkap', 100);
            $table->string('nomor_induk', 30)->unique(); 
            $table->string('email')->unique()->nullable();
            $table->enum('prodi', ['PTIK', 'TEKOM']);
            $table->enum('role', ['pengunjung', 'staff', 'superadmin']);
            $table->string('password_hash', 255);
            $table->string('foto_profil', 255)->nullable();
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('token', 255)->unique();
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });

        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('staff_id')->nullable();
            $table->dateTime('waktu_scan');
            $table->enum('status', ['berhasil', 'gagal'])->default('berhasil');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('staff_id')->references('id_user')->on('users')->onDelete('set null');
        });

        Schema::create('buku', function (Blueprint $table) {
            $table->integer('id_buku')->autoIncrement();
            $table->string('kode_buku', 50)->nullable()->unique();
            $table->string('judul', 255);
            $table->string('penulis', 255)->nullable();
            $table->string('kategori', 100)->nullable(); 
            $table->string('foto_buku', 255)->nullable(); 
            $table->string('tempat', 100)->nullable();
            $table->integer('tahun_buku')->nullable();
            $table->integer('jumlah_tersedia')->nullable();
            $table->timestamps();
        });

        Schema::create('peminjaman', function (Blueprint $table) {
            $table->integer('id_peminjaman')->autoIncrement();
            $table->integer('id_user');
            $table->integer('id_buku');
            $table->string('judul', 255)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->date('tanggal_pinjam')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['dipinjam', 'selesai', 'kembali'])->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');
        });

        Schema::create('riwayat_pengembalian', function (Blueprint $table) {
            $table->integer('id_riwayat')->autoIncrement();
            $table->integer('id_peminjaman');
            $table->date('tanggal_pinjam')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->integer('id_user');
            $table->integer('id_buku');
            $table->timestamps();

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');
        });

        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->enum('jenis', ['Bebas Perpustakaan', 'Donasi Buku']);
            $table->string('nama_lengkap');
            $table->string('nomor_induk');
            $table->string('prodi')->nullable();
            $table->string('status')->default('Menunggu Verifikasi');
            $table->text('pesan')->nullable(); 
            $table->string('jabatan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('judul_buku')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->string('file_balasan')->nullable();
            $table->string('no_surat')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });

        Schema::create('skripsi', function (Blueprint $table) {
            $table->integer('id_skripsi')->autoIncrement();
            $table->integer('user_id')->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('judul', 255);
            $table->text('abstrak')->nullable();
            $table->string('nama', 100);
            $table->string('nim', 30); 
            $table->enum('prodi', ['PTIK', 'TEKOM']);
            $table->integer('tahun_selesai');
            $table->string('file_skripsi', 255);
            $table->string('file_preview_skripsi', 255)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('skripsi');
        Schema::dropIfExists('pengajuan_surats');
        Schema::dropIfExists('riwayat_pengembalian');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('buku');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('qr_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
    }
};