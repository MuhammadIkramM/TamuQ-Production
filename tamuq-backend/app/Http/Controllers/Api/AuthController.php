<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter; 
use Illuminate\Support\Str; 
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->nomor_induk) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 50)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429); 
        }

        $validator = Validator::make($request->all(), [
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'password' => 'required',
        ], [
            'nomor_induk.regex' => 'Nomor Induk / NIM hanya boleh berisi angka.'
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey); 
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('nomor_induk', $request->nomor_induk)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey); 
            return response()->json([
                'success' => false,
                'message' => 'Nomor Induk tidak terdaftar.'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password_hash)) {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi salah.'
            ], 401);
        }

        if (strtolower($user->status_akun) !== 'aktif') {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.'
            ], 403);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'role' => $user->role,
            ]
        ], 200);
    }

    public function register(Request $request)
    {
        $throttleKey = 'register|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 50)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\.\,\-_\']+$/'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/', 'unique:users,nomor_induk'],
            'prodi' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string']
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf, angka, spasi, titik, koma, garis datar, dan garis bawah.',
            'nomor_induk.regex' => 'Nomor Induk / NIM hanya boleh berisi angka.',
            'prodi.regex' => 'Prodi mengandung karakter yang tidak valid.'
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'nama_lengkap' => $request->name,
                'nomor_induk' => $request->nomor_induk,
                'prodi' => $request->prodi,
                'role' => 'pengunjung',
                'password_hash' => Hash::make($request->password),
                'status_akun' => 'aktif',
                'foto_profil' => null
            ]);

            RateLimiter::clear($throttleKey);
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi Berhasil',
                'data' => [
                    'user' => $user
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil!'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $request->merge([
            'email' => $request->email ?: null,
        ]);

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\.\,\-_\']+$/'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id_user . ',id_user'],
        ], [
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf, angka, spasi, titik, koma, garis datar, dan garis bawah.',
            'email.email' => 'Masukkan email yang valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->filled('nama_lengkap')) {
            $user->nama_lengkap = $request->nama_lengkap;
        }

        if ($request->exists('email')) {
            $user->email = $request->email;
        }

        if ($request->hasFile('foto')) {
            try {
                $file = $request->file('foto');
                $path = $file->store('profiles', 'public');
                $user->foto_profil = $path;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Gagal upload foto'], 500);
            }
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini salah.'],
            ]);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diperbarui']);
    }
}