<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengajuanSuratController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\SkripsiController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\LandingContentController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/pengumuman/aktif', [PengumumanController::class, 'aktif']);
Route::get('/landing/hero', [LandingContentController::class, 'heroPublic']);
Route::get('/landing/testimoni', [LandingContentController::class, 'testimoniPublic']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/update-password', [AuthController::class, 'updatePassword']);
    Route::get('/library-status', [DashboardController::class, 'checkStatus']);
    Route::get('/qr/generate', [QrCodeController::class, 'generate']);
    Route::get('/notifikasi-saya', [PengumumanController::class, 'notifikasiSaya']);
    Route::post('/notifikasi/{id}/tandai-dibaca', [PengumumanController::class, 'tandaiDibaca']);
    Route::get('/kunjungan/history', [VisitController::class, 'getHistory']);
    Route::post('/pengajuan', [PengajuanSuratController::class, 'store'])->middleware('throttle:30,1');
    Route::get('/pengajuan', [PengajuanSuratController::class, 'index']);
    Route::get('/download-pendukung/{filename}', [PengajuanSuratController::class, 'downloadPendukung']);
    Route::get('/download-balasan/{filename}', [PengajuanSuratController::class, 'downloadBalasan']);
    Route::get('/preview-pendukung/{filename}', [PengajuanSuratController::class, 'previewPendukung']);
    Route::get('/preview-balasan/{filename}', [PengajuanSuratController::class, 'previewBalasan']);
    Route::get('/user/buku-saya', [PeminjamanController::class, 'indexUser']);
    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/buku/kategori', [BukuController::class, 'kategoriList']);
    Route::get('/skripsi', [SkripsiController::class, 'index']);
    Route::get('/skripsi/saya', [SkripsiController::class, 'riwayatSaya']);
    Route::get('/skripsi/{id}/preview', [SkripsiController::class, 'preview'])->middleware('throttle:60,1');
    Route::get('/skripsi/{id}/download', [SkripsiController::class, 'download'])->middleware('throttle:60,1');
    Route::post('/skripsi', [SkripsiController::class, 'ajukan'])->middleware('throttle:20,1');
    Route::get('/skripsi/{id}/preview-file', [SkripsiController::class, 'previewFilePreview'])->middleware('throttle:60,1');
    Route::get('/skripsi/{id}/download-file', [SkripsiController::class, 'downloadFilePreview'])->middleware('throttle:60,1');

    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'index']);
        Route::post('/staff/library-status', [DashboardController::class, 'updateStatus']);
        Route::post('/scan-qr', [VisitController::class, 'scanQr']);
        Route::post('/kunjungan/scan', [VisitController::class, 'scanQr']);
        Route::post('/pengajuan/{id}/verify', [PengajuanSuratController::class, 'verify'])->middleware('throttle:30,1');
        Route::patch('/pengajuan/{id}/update-pesan', [PengajuanSuratController::class, 'updatePesan'])->middleware('throttle:30,1');
        Route::post('/pengajuan/{id}/update-balasan', [PengajuanSuratController::class, 'updateBalasan'])->middleware('throttle:30,1');
        Route::post('/buku', [BukuController::class, 'store'])->middleware('throttle:30,1');
        Route::put('/buku/{id}', [BukuController::class, 'update'])->middleware('throttle:30,1');
        Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->middleware('throttle:30,1');
        Route::post('/staff/skripsi', [SkripsiController::class, 'store'])->middleware('throttle:30,1');
        Route::post('/staff/skripsi/{id}', [SkripsiController::class, 'update'])->middleware('throttle:30,1');
        Route::delete('/staff/skripsi/{id}', [SkripsiController::class, 'destroy'])->middleware('throttle:30,1');
        Route::post('/staff/skripsi/{id}/verify', [SkripsiController::class, 'verify'])->middleware('throttle:30,1');
        Route::get('/staff/users-list', [PeminjamanController::class, 'getUsersList']);
        Route::get('/staff/peminjaman', [PeminjamanController::class, 'indexAdmin']);
        Route::post('/staff/peminjaman', [PeminjamanController::class, 'store']);
        Route::post('/staff/peminjaman/{id}/return', [PeminjamanController::class, 'returnBook']);
        Route::post('/staff/peminjaman/scan-user', [PeminjamanController::class, 'scanUser']);
        Route::get('/staff/pengumuman', [PengumumanController::class, 'index']);
        Route::get('/staff/pengumuman/cari-user', [PengumumanController::class, 'cariUser']);
        Route::post('/staff/pengumuman', [PengumumanController::class, 'store'])->middleware('throttle:30,1');
        Route::put('/staff/pengumuman/{id}', [PengumumanController::class, 'update'])->middleware('throttle:30,1');
        Route::post('/staff/pengumuman/{id}/toggle', [PengumumanController::class, 'toggleStatus'])->middleware('throttle:30,1');
        Route::delete('/staff/pengumuman/{id}', [PengumumanController::class, 'destroy'])->middleware('throttle:30,1');
        Route::get('/staff/landing/hero', [LandingContentController::class, 'heroIndex']);
        Route::post('/staff/landing/hero', [LandingContentController::class, 'heroStore'])->middleware('throttle:30,1');
        Route::post('/staff/landing/hero/reorder', [LandingContentController::class, 'heroReorder'])->middleware('throttle:60,1');
        Route::post('/staff/landing/hero/{id}', [LandingContentController::class, 'heroUpdate'])->middleware('throttle:30,1');
        Route::post('/staff/landing/hero/{id}/toggle', [LandingContentController::class, 'heroToggle'])->middleware('throttle:30,1');
        Route::delete('/staff/landing/hero/{id}', [LandingContentController::class, 'heroDestroy'])->middleware('throttle:30,1');
        Route::get('/staff/landing/testimoni', [LandingContentController::class, 'testimoniIndex']);
        Route::post('/staff/landing/testimoni', [LandingContentController::class, 'testimoniStore'])->middleware('throttle:30,1');
        Route::post('/staff/landing/testimoni/reorder', [LandingContentController::class, 'testimoniReorder'])->middleware('throttle:60,1');
        Route::post('/staff/landing/testimoni/{id}', [LandingContentController::class, 'testimoniUpdate'])->middleware('throttle:30,1');
        Route::post('/staff/landing/testimoni/{id}/toggle', [LandingContentController::class, 'testimoniToggle'])->middleware('throttle:30,1');
        Route::delete('/staff/landing/testimoni/{id}', [LandingContentController::class, 'testimoniDestroy'])->middleware('throttle:30,1');
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users', [AdminUserController::class, 'store']);
        Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
        Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
    });

});