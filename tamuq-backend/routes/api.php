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

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/update-password', [AuthController::class, 'updatePassword']);
    Route::get('/library-status', [DashboardController::class, 'checkStatus']);
    Route::get('/qr/generate', [QrCodeController::class, 'generate']);
    Route::get('/kunjungan/history', [VisitController::class, 'getHistory']);
    Route::post('/pengajuan', [PengajuanSuratController::class, 'store'])->middleware('throttle:30,1');
    Route::get('/pengajuan', [PengajuanSuratController::class, 'index']);
    Route::get('/download-pendukung/{filename}', [PengajuanSuratController::class, 'downloadPendukung']);
    Route::get('/download-balasan/{filename}', [PengajuanSuratController::class, 'downloadBalasan']);
    Route::get('/user/buku-saya', [PeminjamanController::class, 'indexUser']);
    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/buku/kategori', [BukuController::class, 'kategoriList']);
    Route::get('/skripsi', [SkripsiController::class, 'index']);
    Route::get('/skripsi/{id}/preview', [SkripsiController::class, 'preview'])->middleware('throttle:60,1');
    Route::get('/skripsi/{id}/download', [SkripsiController::class, 'download'])->middleware('throttle:60,1');

    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [DashboardController::class, 'index']);
        Route::post('/staff/library-status', [DashboardController::class, 'updateStatus']);
        Route::post('/scan-qr', [VisitController::class, 'scanQr']);
        Route::post('/kunjungan/scan', [VisitController::class, 'scanQr']);
        Route::post('/pengajuan/{id}/verify', [PengajuanSuratController::class, 'verify'])->middleware('throttle:30,1');
        Route::post('/buku', [BukuController::class, 'store'])->middleware('throttle:30,1');
        Route::put('/buku/{id}', [BukuController::class, 'update'])->middleware('throttle:30,1');
        Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->middleware('throttle:30,1');
        Route::post('/staff/skripsi', [SkripsiController::class, 'store'])->middleware('throttle:30,1');
        Route::post('/staff/skripsi/{id}', [SkripsiController::class, 'update'])->middleware('throttle:30,1');
        Route::delete('/staff/skripsi/{id}', [SkripsiController::class, 'destroy'])->middleware('throttle:30,1');
        Route::get('/staff/users-list', [PeminjamanController::class, 'getUsersList']);
        Route::get('/staff/peminjaman', [PeminjamanController::class, 'indexAdmin']);
        Route::post('/staff/peminjaman', [PeminjamanController::class, 'store']);
        Route::post('/staff/peminjaman/{id}/return', [PeminjamanController::class, 'returnBook']);
        Route::post('/staff/peminjaman/scan-user', [PeminjamanController::class, 'scanUser']);
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users', [AdminUserController::class, 'store']);
        Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
        Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
    });

});