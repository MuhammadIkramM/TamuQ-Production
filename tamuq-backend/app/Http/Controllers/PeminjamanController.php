<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\RiwayatPengembalian;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function getUsersList()
    {
        try {
            $users = User::select('id_user', 'nama_lengkap', 'nomor_induk')
                ->where('role', '!=', 'superadmin')
                ->orderBy('nama_lengkap', 'asc')
                ->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil user: ' . $e->getMessage()], 500);
        }
    }

    public function indexAdmin()
    {
        try {
            $aktif = Peminjaman::with(['user'])
                ->where('status', 'dipinjam')
                ->orderBy('tanggal_pinjam', 'desc')
                ->get()
                ->map(function ($item) {
                    $foto = $item->user->foto_profil ?? null;
                    $nama = $item->user->nama_lengkap ?? 'User';
                    $urlFoto = null;

                    if ($foto) {
                        if (str_contains($foto, 'http')) {
                            $urlFoto = $foto;
                        } else {
                            $urlFoto = asset('storage/' . $foto);
                        }
                    } else {
                        $urlFoto = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=128';
                    }

                    $batas = $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->startOfDay() : null;
                    $sekarang = Carbon::now()->startOfDay();
                    $sisaHari = $batas ? $sekarang->diffInDays($batas, false) : 0;

                    return [
                        'id' => $item->id_peminjaman,
                        'namaPeminjam' => $nama,
                        'noHp' => $item->no_hp,
                        'foto_profil' => $urlFoto,
                        'judulBuku' => $item->judul,
                        'tanggalPinjam' => $item->tanggal_pinjam,
                        'batasPengembalian' => $item->tanggal_kembali,
                        'sisaHari' => $sisaHari,
                        'status' => $item->status
                    ];
                });

            $riwayat = RiwayatPengembalian::with(['user', 'buku'])
                ->orderBy('id_riwayat', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id_riwayat,
                    'namaPeminjam' => $item->user->nama_lengkap ?? 'User Dihapus',
                    'judulBuku' => $item->buku->judul ?? 'Buku Dihapus',
                    'tanggalPinjam' => $item->tanggal_pinjam,
                    'tanggalKembali' => $item->tanggal_kembali,
                    'status' => 'Selesai'
                ]);

            return response()->json([
                'aktif' => $aktif,
                'riwayat' => $riwayat
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memuat data: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'id_buku' => 'required|exists:buku,id_buku',
            'tanggalPinjam' => 'required|date',
            'tanggalKembali' => 'required|date|after_or_equal:tanggalPinjam',
            'noHp' => ['required', 'string', 'regex:/^[0-9]+$/'] 
        ], [
            'noHp.regex' => 'Nomor HP hanya boleh berisi angka tanpa karakter lain (+, -, atau spasi).',
            'tanggalKembali.after_or_equal' => 'Tanggal kembali tidak boleh kurang dari tanggal pinjam.'
        ]);

        DB::beginTransaction();
        try {
            $buku = Buku::where('id_buku', $request->id_buku)->lockForUpdate()->first();

            if (!$buku || $buku->jumlah_tersedia <= 0) {
                DB::rollBack();
                return response()->json(['message' => 'Stok buku habis.'], 400);
            }

            $buku->decrement('jumlah_tersedia');

            $peminjaman = Peminjaman::create([
                'id_user' => $request->id_user,
                'id_buku' => $buku->id_buku,
                'judul'   => $buku->judul,
                'no_hp'   => $request->noHp,
                'tanggal_pinjam' => $request->tanggalPinjam,
                'tanggal_kembali' => $request->tanggalKembali,
                'status' => 'dipinjam'
            ]);

            DB::commit();
            return response()->json(['message' => 'Peminjaman berhasil dicatat!', 'data' => $peminjaman]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function returnBook($id)
    {
        $peminjaman = Peminjaman::where('id_peminjaman', $id)->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        if ($peminjaman->status !== 'dipinjam') {
             return response()->json(['message' => 'Buku sudah dikembalikan sebelumnya.'], 400);
        }

        DB::beginTransaction();
        try {
            RiwayatPengembalian::create([
                'id_peminjaman'   => $peminjaman->id_peminjaman,
                'id_user'         => $peminjaman->id_user,
                'id_buku'         => $peminjaman->id_buku,
                'tanggal_pinjam'  => $peminjaman->tanggal_pinjam,
                'tanggal_kembali' => Carbon::now(),
            ]);

            $buku = Buku::where('id_buku', $peminjaman->id_buku)->lockForUpdate()->first();
            if ($buku) {
                $buku->increment('jumlah_tersedia');
            }

            $peminjaman->update(['status' => 'kembali']);

            DB::commit();
            return response()->json(['message' => 'Buku berhasil dikembalikan']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function indexUser()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized - Silakan login kembali'], 401);
            }

            $userId = $user->id_user;

            $aktif = Peminjaman::where('id_user', $userId)
                ->where('status', 'dipinjam')
                ->get()
                ->map(function ($item) {
                    $batas = $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->startOfDay() : null;
                    $sekarang = Carbon::now()->startOfDay();
                    $sisaHari = $batas ? $sekarang->diffInDays($batas, false) : 0;

                    return [
                        'id' => $item->id_peminjaman,
                        'judulBuku' => $item->judul,
                        'tanggalPinjam' => Carbon::parse($item->tanggal_pinjam)->format('d M Y'),
                        'batasPengembalian' => $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d M Y') : '-',
                        'sisaHari' => $sisaHari,
                        'status' => 'Dipinjam'
                    ];
                });

            $riwayat = RiwayatPengembalian::with('buku')
                ->where('id_user', $userId)
                ->orderBy('tanggal_kembali', 'desc')
                ->get()
                ->map(fn($item) => [
                    'id' => $item->id_riwayat,
                    'judulBuku' => $item->buku->judul ?? 'Judul Tidak Ditemukan',
                    'tanggalPinjam' => Carbon::parse($item->tanggal_pinjam)->format('d M Y'),
                    'tanggalKembali' => Carbon::parse($item->tanggal_kembali)->format('d M Y'),
                    'status' => 'Selesai'
                ]);

            return response()->json(['aktif' => $aktif, 'riwayat' => $riwayat]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function scanUser(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $tokenString = $request->qr_code;

        $tokenData = DB::table('qr_tokens')->where('token', $tokenString)->first();

        if (!$tokenData) {
            return response()->json(['message' => 'QR Code tidak ditemukan.'], 404);
        }

        if ($tokenData->status === 'used') {
            return response()->json(['message' => 'QR Code sudah digunakan untuk kunjungan.'], 400);
        }

        if (Carbon::now()->gt($tokenData->expires_at)) {
            return response()->json(['message' => 'QR Code kadaluarsa, minta pengunjung generate ulang.'], 400);
        }

        $user = User::where('id_user', $tokenData->user_id)->first();

        if (!$user) {
            return response()->json(['message' => 'Data user tidak ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_user'       => $user->id_user,
                'nama_lengkap'  => $user->nama_lengkap,
                'nomor_induk'   => $user->nomor_induk,
                'prodi'         => $user->prodi,
            ]
        ]);
    }
}