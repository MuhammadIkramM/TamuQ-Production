<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrToken; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function generate(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $tokenString = Str::random(60);

    try {
        $qrToken = QrToken::create([
            'user_id'     => $user->id_user,
            'token'       => $tokenString, 
            'status'      => 'active',
            'expires_at'  => Carbon::now()->addSeconds(30),
        ]);

        return response()->json([
            'status'     => 'success',
            'qr_code'    => $tokenString,
            'expires_in' => 30
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal Database',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
