<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::orderBy('created_at', 'desc')->get();
        return response()->json($buku);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'kode_buku'       => ['nullable', 'string', 'max:50', 'unique:buku,kode_buku', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'judul'           => 'required|string|max:255',
            'penulis'         => 'nullable|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'tempat'          => 'nullable|string|max:100',
            'tahun_buku'      => 'nullable|integer|min:1900|max:2100',
            'jumlah_tersedia' => 'required|integer|min:0',
            'foto_buku'       => 'nullable|image|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:8192',
        ], [
            'kode_buku.regex' => 'Kode buku hanya boleh berisi huruf, angka, dan garis tengah (-).'
        ]);

        $buku = Buku::create($validator);

        if ($request->hasFile('foto_buku')) {
            $path = $request->file('foto_buku')->store('buku_foto', 'public');
            $buku->foto_buku = $path;
            $buku->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data'    => $buku
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kode_buku'       => ['nullable', 'string', 'max:50', 'unique:buku,kode_buku,'.$id.',id_buku', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'judul'           => 'required|string|max:255',
            'penulis'         => 'nullable|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'tempat'          => 'nullable|string|max:100',
            'tahun_buku'      => 'nullable|integer|min:1900|max:2100',
            'jumlah_tersedia' => 'required|integer|min:0',
            'foto_buku'       => 'nullable|image|mimes:jpeg,jpg,png|mimetypes:image/jpeg,image/png|max:8192',
        ], [
            'kode_buku.regex' => 'Kode buku hanya boleh berisi huruf, angka, dan garis tengah (-).'
        ]);

        $buku->update($validated);

        if ($request->hasFile('foto_buku')) {
            if ($buku->foto_buku && Storage::disk('public')->exists($buku->foto_buku)) {
                Storage::disk('public')->delete($buku->foto_buku);
            }
            $path = $request->file('foto_buku')->store('buku_foto', 'public');
            $buku->foto_buku = $path;
            $buku->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diupdate',
            'data'    => $buku
        ]);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        if ($buku->foto_buku && Storage::disk('public')->exists($buku->foto_buku)) {
            Storage::disk('public')->delete($buku->foto_buku);
        }

        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    }

    public function kategoriList()
    {
        $kategori = Buku::whereNotNull('kategori')->where('kategori', '!=', '')->distinct()->orderBy('kategori')->pluck('kategori');
        return response()->json($kategori);
    }
}