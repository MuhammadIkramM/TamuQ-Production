<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingHero;
use App\Models\LandingTestimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingContentController extends Controller
{

    public function heroPublic()
    {
        $data = LandingHero::aktif()->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function heroIndex()
    {
        $data = LandingHero::orderBy('urutan')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function heroStore(Request $request)
    {
        $validated = $request->validate([
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        $path = $request->file('gambar')->store('landing_hero', 'public');

        $hero = LandingHero::create([
            'gambar' => $path,
            'urutan' => $validated['urutan'] ?? LandingHero::count(),
            'status' => 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gambar hero berhasil ditambahkan',
            'data'    => $hero,
        ], 201);
    }

    public function heroUpdate(Request $request, $id)
    {
        $hero = LandingHero::findOrFail($id);

        $validated = $request->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        if ($request->hasFile('gambar')) {
            if ($hero->gambar && Storage::disk('public')->exists($hero->gambar)) {
                Storage::disk('public')->delete($hero->gambar);
            }
            $path = $request->file('gambar')->store('landing_hero', 'public');
            $hero->gambar = $path;
        }

        if (isset($validated['status'])) {
            $hero->status = $validated['status'];
        }

        $hero->save();

        return response()->json([
            'success' => true,
            'message' => 'Gambar hero berhasil diperbarui',
            'data'    => $hero,
        ]);
    }

    public function heroReorder(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:landing_hero,id',
        ]);

        foreach ($validated['ids'] as $index => $id) {
            LandingHero::where('id', $id)->update(['urutan' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan berhasil diperbarui',
        ]);
    }

    public function heroToggle($id)
    {
        $hero = LandingHero::findOrFail($id);
        $hero->status = $hero->status === 'aktif' ? 'nonaktif' : 'aktif';
        $hero->save();

        return response()->json([
            'success' => true,
            'data'    => $hero,
        ]);
    }

    public function heroDestroy($id)
    {
        $hero = LandingHero::findOrFail($id);

        if ($hero->gambar && Storage::disk('public')->exists($hero->gambar)) {
            Storage::disk('public')->delete($hero->gambar);
        }

        $hero->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gambar hero berhasil dihapus',
        ]);
    }

    public function testimoniPublic()
    {
        $data = LandingTestimoni::aktif()->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function testimoniIndex()
    {
        $data = LandingTestimoni::orderBy('urutan')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function testimoniStore(Request $request)
    {
        $validated = $request->validate([
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'nama'    => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'kutipan' => 'required|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('landing_testimoni', 'public');
        }

        $testimoni = LandingTestimoni::create([
            'foto'    => $fotoPath,
            'nama'    => $validated['nama'],
            'jabatan' => $validated['jabatan'] ?? null,
            'kutipan' => $validated['kutipan'],
            'urutan'  => LandingTestimoni::count(),
            'status'  => 'aktif',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil ditambahkan',
            'data'    => $testimoni,
        ], 201);
    }

    public function testimoniUpdate(Request $request, $id)
    {
        $testimoni = LandingTestimoni::findOrFail($id);

        $validated = $request->validate([
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'nama'    => 'sometimes|required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'kutipan' => 'sometimes|required|string',
            'status'  => 'nullable|in:aktif,nonaktif',
        ]);

        if ($request->hasFile('foto')) {
            if ($testimoni->foto && Storage::disk('public')->exists($testimoni->foto)) {
                Storage::disk('public')->delete($testimoni->foto);
            }
            $testimoni->foto = $request->file('foto')->store('landing_testimoni', 'public');
        }

        if (isset($validated['nama'])) {
            $testimoni->nama = $validated['nama'];
        }

        if (array_key_exists('jabatan', $validated)) {
            $testimoni->jabatan = $validated['jabatan'];
        }

        if (isset($validated['kutipan'])) {
            $testimoni->kutipan = $validated['kutipan'];
        }

        if (isset($validated['status'])) {
            $testimoni->status = $validated['status'];
        }

        $testimoni->save();

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil diperbarui',
            'data'    => $testimoni,
        ]);
    }

    public function testimoniReorder(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:landing_testimoni,id',
        ]);

        foreach ($validated['ids'] as $index => $id) {
            LandingTestimoni::where('id', $id)->update(['urutan' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan berhasil diperbarui',
        ]);
    }

    public function testimoniToggle($id)
    {
        $testimoni = LandingTestimoni::findOrFail($id);
        $testimoni->status = $testimoni->status === 'aktif' ? 'nonaktif' : 'aktif';
        $testimoni->save();

        return response()->json([
            'success' => true,
            'data'    => $testimoni,
        ]);
    }

    public function testimoniDestroy($id)
    {
        $testimoni = LandingTestimoni::findOrFail($id);

        if ($testimoni->foto && Storage::disk('public')->exists($testimoni->foto)) {
            Storage::disk('public')->delete($testimoni->foto);
        }

        $testimoni->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil dihapus',
        ]);
    }
}
