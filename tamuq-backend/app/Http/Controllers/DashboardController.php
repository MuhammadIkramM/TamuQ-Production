<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function checkStatus()
    {
        $setting = DB::table('settings')->where('key', 'library_status')->first();
        $isOpen = $setting && $setting->value === 'buka';

        return response()->json([
            'is_open' => $isOpen
        ]);
    }
    
    public function index()
    {
        $setting = DB::table('settings')->where('key', 'library_status')->first();
        $isOpen = $setting && $setting->value === 'buka';

        $totalBuku = DB::table('buku')->count();
        $peminjamanAktif = DB::table('peminjaman')->where('status', 'dipinjam')->count();

        $today = Carbon::today();
        $pengunjungHariIni = DB::table('kunjungan')
            ->whereDate('waktu_scan', $today)
            ->count();
        $chartData = [];
        $hours = [8, 10, 12, 14, 16];

        foreach ($hours as $hour) {
            $count = DB::table('kunjungan')
                ->whereDate('waktu_scan', $today)
                ->whereTime('waktu_scan', '>=', "$hour:00:00")
                ->whereTime('waktu_scan', '<', ($hour + 2) . ":00:00")
                ->count();
            $chartData[] = $count;
        }
        
        $ptikCount = DB::table('users')->where('prodi', 'PTIK')->count();
        $tekomCount = DB::table('users')->where('prodi', 'TEKOM')->count();

        return response()->json([
            'is_open' => $isOpen,
            'total_buku' => $totalBuku,
            'pengunjung_hari_ini' => $pengunjungHariIni,
            'peminjaman_aktif' => $peminjamanAktif,
            'chart_data' => $chartData,
            'demografi' => [$ptikCount, $tekomCount]
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);

        $value = $request->status ? 'buka' : 'tutup';

        $affected = DB::table('settings')
            ->where('key', 'library_status')
            ->update(['value' => $value, 'updated_at' => now()]);

        if ($affected === 0) {
            DB::table('settings')->insert([
                'key' => 'library_status',
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Status berhasil diperbarui',
            'status' => $value
        ]);
    }
}
