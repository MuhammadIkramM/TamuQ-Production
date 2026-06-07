<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanSurat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 

class PengajuanSuratController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Sesi tidak valid atau Anda belum login.'], 401);
        }
        $query = PengajuanSurat::with('user')->orderBy('created_at', 'desc');

        if (strtolower($user->role) === 'pengunjung') {
            $query->where('user_id', $user->id_user);
        }

        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis', $request->jenis);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis'          => 'required|in:Bebas Perpustakaan,Donasi Buku',
            'file_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', 
            'no_hp'          => 'nullable|string|max:20', 
            'alamat'         => 'nullable|string|max:500',
            'jabatan'        => 'nullable|string|max:100',
            'prodi'          => 'nullable|string|max:100',
            'judul_buku'     => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            $user = $request->user();

            $cekDuplikat = PengajuanSurat::where('user_id', $user->id_user)
                ->where('jenis', $request->jenis)
                ->where('judul_buku', $request->judul_buku ?? '-')
                ->where('created_at', '>=', now()->subMinutes(2))
                ->first();

            if ($cekDuplikat) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pengajuan berhasil dikirim.',
                    'data' => $cekDuplikat
                ], 200); 
            }
            
            $prodiUser = $user->prodi ?? ($request->prodi ?? '-');

            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $filePath = $request->file('file_pendukung')->store('pengajuan_files', 'local');
            }

            if (!$filePath) {
                return response()->json(['message' => 'Gagal mengunggah file pendukung.'], 500);
            }

            $pengajuan = PengajuanSurat::create([
                'user_id'        => $user->id_user,
                'jenis'          => $request->jenis,
                'nama_lengkap'   => $user->nama_lengkap,
                'nomor_induk'    => $user->nomor_induk,
                'prodi'          => $prodiUser,
                'status'         => 'Menunggu Verifikasi',
                'no_hp'          => $request->no_hp ?? '-',
                'alamat'         => $request->alamat ?? '-',
                'jabatan'        => $request->jabatan ?? 'Mahasiswa',
                'judul_buku'     => $request->judul_buku ?? '-',
                'file_pendukung' => $filePath,
                'no_surat'       => '-', 
            ]);

            if ($request->jenis === 'Donasi Buku') {
                $urlWebhook = 'https://script.google.com/macros/s/AKfycbyX68Y2Tm7-X7-G36NRQahyI3neO3zEQN9DWYLHaFEzsRHaSJ9hE5sSSTKBZg-EpCduXA/exec';
                
                try {
                    $linkFile = url('api/pengajuan/download/' . basename($filePath)); 

                    $response = Http::timeout(10)->post($urlWebhook, [
                        'timestamp'      => $pengajuan->created_at->format('Y-m-d H:i:s'),
                        'email'          => $user->email ?? '-', 
                        'nama'           => $user->nama_lengkap,
                        'nim'            => $user->nomor_induk,
                        'prodi'          => $prodiUser,
                        'alamat'         => $request->alamat ?? '-',
                        'no_hp'          => $request->no_hp ?? '-',
                        'judul_buku'     => $request->judul_buku ?? '-',
                        'bukti_transfer' => $linkFile
                    ]);

                    if ($response->successful() && $response->json('no_surat')) {
                        $pengajuan->update([
                            'no_surat' => $response->json('no_surat')
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Webhook Spreadsheet Error: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pengajuan berhasil dikirim.',
                'data' => $pengajuan
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function verify(Request $request, $id)
    {
        if (strtolower($request->user()->role) === 'pengunjung') {
            return response()->json(['message' => 'Unauthorized. Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status'       => 'required|in:Diterima,Ditolak,Selesai', 
            'file_balasan' => 'required_if:status,Diterima|nullable|file|mimes:pdf,jpg,png|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $pengajuan = PengajuanSurat::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        try {
            $pengajuan->status = $request->status === 'Diterima' ? 'Selesai' : $request->status;

            if ($request->hasFile('file_balasan')) {
                if($pengajuan->file_balasan && Storage::disk('local')->exists($pengajuan->file_balasan)){
                    Storage::disk('local')->delete($pengajuan->file_balasan);
                }

                $path = $request->file('file_balasan')->store('balasan_files', 'local');
                $pengajuan->file_balasan = $path;
            }

            $pengajuan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Status pengajuan berhasil diperbarui menjadi ' . $pengajuan->status,
                'data' => $pengajuan
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal verifikasi: ' . $e->getMessage()], 500);
        }
    }

    public function downloadPendukung(Request $request, $filename)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Sesi habis atau Anda belum login.'], 401);
        }

        $user = $request->user();
        $pengajuan = PengajuanSurat::where('file_pendukung', 'LIKE', '%' . $filename . '%')->first();

        if (!$pengajuan) {
            return response()->json(['message' => 'Data pengajuan tidak ditemukan di database.'], 404);
        }

        if (strtolower($user->role) === 'pengunjung' && $pengajuan->user_id != $user->id_user) {
            return response()->json(['message' => 'Akses Ditolak! Anda tidak berhak mengunduh file ini.'], 403);
        }

        $dbPath = $pengajuan->file_pendukung;

        if (Storage::disk('local')->exists($dbPath)) return Storage::disk('local')->download($dbPath);
        elseif (Storage::disk('public')->exists($dbPath)) return Storage::disk('public')->download($dbPath);
        elseif (Storage::disk('local')->exists('pengajuan_files/' . $filename)) return Storage::disk('local')->download('pengajuan_files/' . $filename);
        elseif (Storage::disk('public')->exists('pengajuan_files/' . $filename)) return Storage::disk('public')->download('pengajuan_files/' . $filename);

        return response()->json(['message' => 'File fisik tidak ditemukan di server.'], 404);
    }

    public function downloadBalasan(Request $request, $filename)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Sesi habis atau Anda belum login.'], 401);
        }

        $user = $request->user();
        $pengajuan = PengajuanSurat::where('file_balasan', 'LIKE', '%' . $filename . '%')->first();

        if (!$pengajuan) {
            return response()->json(['message' => 'Data pengajuan tidak ditemukan di database.'], 404);
        }

        if (strtolower($user->role) === 'pengunjung' && $pengajuan->user_id != $user->id_user) {
            return response()->json(['message' => 'Akses Ditolak! Anda tidak berhak mengunduh file ini.'], 403);
        }

        $dbPath = $pengajuan->file_balasan;

        if (Storage::disk('local')->exists($dbPath)) return Storage::disk('local')->download($dbPath);
        elseif (Storage::disk('public')->exists($dbPath)) return Storage::disk('public')->download($dbPath);
        elseif (Storage::disk('local')->exists('balasan_files/' . $filename)) return Storage::disk('local')->download('balasan_files/' . $filename);
        elseif (Storage::disk('public')->exists('balasan_files/' . $filename)) return Storage::disk('public')->download('balasan_files/' . $filename);

        return response()->json(['message' => 'File balasan fisik tidak ditemukan di server.'], 404);
    }
}