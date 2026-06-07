<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = Kunjungan::with('user')
                    ->orderBy('waktu_scan', 'desc')
                    ->limit(500)
                    ->get();

        return response()->json($riwayat);
    }
}
