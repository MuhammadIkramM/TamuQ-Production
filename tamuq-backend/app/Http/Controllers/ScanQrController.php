<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrToken;
use App\Models\User;
use App\Models\Kunjungan;

class ScanQrController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $qrData = QrToken::where('token', $request->qr_code)->first();

        if (!$qrData) {
            return response()->json(['status' => 'error', 'message' => 'QR Code Tidak Valid'], 404);
        }

        if ($qrData->status === 'used') {
             return response()->json(['status' => 'error', 'message' => 'QR Code sudah digunakan sebelumnya'], 400);
        }

        $user = User::where('id_user', $qrData->user_id)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User Tidak Ditemukan'], 404);
        }

        $qrData->update(['status' => 'used']);

        Kunjungan::create([
            'user_id'    => $user->id_user,
            'waktu_scan' => now(),
            'status'     => 'berhasil',
            'keterangan' => 'Scan Masuk'
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama_lengkap' => $user->nama_lengkap,
                'nomor_induk'  => $user->nomor_induk,
                'prodi'        => $user->prodi,
                'foto'         => $user->foto_profil,
                'role'         => $user->role,
            ]
        ]);
    }
}
