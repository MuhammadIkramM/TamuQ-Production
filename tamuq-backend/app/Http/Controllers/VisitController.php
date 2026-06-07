<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Kunjungan; 

class VisitController extends Controller
{
    public function scanQr(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $tokenString = $request->qr_code;
        $staff = $request->user();

        $tokenData = DB::table('qr_tokens')->where('token', $tokenString)->first();

        if (!$tokenData) {
            return response()->json(['message' => 'QR Code tidak ditemukan.'], 404);
        }
        if ($tokenData->status === 'used') {
            return response()->json(['message' => 'QR Code SUDAH DIPAKAI.'], 400);
        }
        if (Carbon::now()->gt($tokenData->expires_at)) {
            return response()->json(['message' => 'QR Code KADALUARSA.'], 400);
        }

        try {
            DB::beginTransaction();

            DB::table('qr_tokens')
                ->where('id', $tokenData->id)
                ->update(['status' => 'used']);

            $kunjungan = Kunjungan::create([
                'user_id'    => $tokenData->user_id,
                'staff_id'   => $staff->id_user,
                'waktu_scan' => now(),
                'status'     => 'berhasil',
                'keterangan' => 'Scan via QR'
            ]);

            $visitor = User::where('id_user', $tokenData->user_id)->first();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Scan Berhasil',
                'data' => [
                    'nama'         => $visitor->nama_lengkap,
                    'nama_lengkap' => $visitor->nama_lengkap,
                    'nim'          => $visitor->nomor_induk,
                    'nomor_induk'  => $visitor->nomor_induk,
                    'prodi'        => $visitor->prodi,
                    'foto'         => $visitor->foto_profil,
                    'waktu'        => $kunjungan->waktu_scan
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();

        $query = Kunjungan::with('user')->orderBy('created_at', 'desc');

        if ($user->role !== 'staff' && $user->role !== 'superadmin') {
            $query->where('user_id', $user->id_user);
        }

        $dataRaw = $query->limit(50)->get();
        $mappedData = $dataRaw->map(function ($item) {
            $userData = $item->user;

            return [
                'id'            => $item->id,
                'waktu_scan'    => $item->waktu_scan,
                'waktu_display' => Carbon::parse($item->waktu_scan)->format('D, d M Y H:i'),
                'status'        => $item->status,
                'nama_lengkap'  => $userData ? $userData->nama_lengkap : 'User Tidak Dikenal',
                'nama'          => $userData ? $userData->nama_lengkap : 'User Tidak Dikenal',
                'name'          => $userData ? $userData->nama_lengkap : 'User Tidak Dikenal',
                'nomor_induk'   => $userData ? $userData->nomor_induk : '-',
                'nim'           => $userData ? $userData->nomor_induk : '-',
                'prodi'         => $userData ? $userData->prodi : '-',
                'user'          => $userData
            ];
        });

        return response()->json($mappedData);
    }
}
