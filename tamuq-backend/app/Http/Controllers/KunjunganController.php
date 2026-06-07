<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use Illuminate\Support\Facades\Auth;

class KunjunganController extends Controller
{
    public function history(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'staff' || $user->role === 'superadmin') {
            $history = Kunjungan::with('user')
                        ->orderBy('waktu_scan', 'desc')
                        ->get();
        } else {
            $history = Kunjungan::with('user') 
                        ->where('user_id', $user->id_user)
                        ->orderBy('waktu_scan', 'desc')
                        ->get();
        }

        return response()->json($history);
    }
}