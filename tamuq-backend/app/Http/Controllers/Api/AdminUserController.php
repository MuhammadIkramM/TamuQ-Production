<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id_user', 'desc')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:100',
            'nomor_induk'  => 'required|string|unique:users,nomor_induk',
            'prodi'        => 'required',
            'role'         => 'required',
            'status'       => 'required',
            'password'     => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $user = User::create([
                'nama_lengkap'  => $request->nama_lengkap,
                'nomor_induk'   => $request->nomor_induk,
                'prodi'         => $request->prodi,
                'role'          => $request->role,
                'status_akun'   => strtolower($request->status) === 'aktif' ? 'aktif' : 'nonaktif',
                'password_hash' => Hash::make($request->password),
                'foto_profil'   => null
            ]);

            return response()->json([
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal Insert DB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id_user', $id)->first();
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:100',
            'nomor_induk'  => 'required|string|unique:users,nomor_induk,' . $id . ',id_user',
            'prodi'        => 'required',
            'role'         => 'required',
            'status'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_induk'  => $request->nomor_induk,
            'prodi'        => $request->prodi,
            'role'         => $request->role,
            'status_akun'  => strtolower($request->status) === 'aktif' ? 'aktif' : 'nonaktif',
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password_hash' => Hash::make($request->password)
            ]);
        }

        return response()->json(['message' => 'Data user diperbarui', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::where('id_user', $id)->first();
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}
