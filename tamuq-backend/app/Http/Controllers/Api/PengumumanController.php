<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function aktif(Request $request)
    {
        $user = $request->user();

        $query = Pengumuman::aktif();

        $query->where(function ($q) use ($user) {
            $q->where('target', 'semua');

            if ($user) {
                $role = strtolower($user->role ?? '');
                $grupTarget = in_array($role, ['staff', 'superadmin']) ? 'staff' : 'pengunjung';

                $q->orWhere('target', $grupTarget);

                $q->orWhere(function ($sub) use ($user) {
                    $sub->where('target', 'individu')
                        ->whereHas('penerima', function ($rel) use ($user) {
                            $rel->where('users.id_user', $user->id_user);
                        });
                });
            }
        });

        $query->orderByRaw("FIELD(tipe, 'darurat', 'penting', 'info')")
            ->orderBy('created_at', 'desc');

        $pengumuman = $query->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman,
        ]);
    }

    public function notifikasiSaya(Request $request)
    {
        $user = $request->user();
        $role = strtolower($user->role ?? '');
        $grupTarget = in_array($role, ['staff', 'superadmin']) ? 'staff' : 'pengunjung';

        $pengumuman = Pengumuman::where(function ($q) use ($user, $grupTarget) {
                $q->where('target', 'semua')
                  ->orWhere('target', $grupTarget)
                  ->orWhere(function ($sub) use ($user) {
                      $sub->where('target', 'individu')
                          ->whereHas('penerima', function ($rel) use ($user) {
                              $rel->where('users.id_user', $user->id_user);
                          });
                  });
            })
            ->with(['penerima' => function ($q) use ($user) {
                $q->where('users.id_user', $user->id_user);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman,
        ]);
    }

    public function tandaiDibaca(Request $request, $id)
    {
        $user = $request->user();

        $pengumuman = Pengumuman::where('id', $id)
            ->where('target', 'individu')
            ->firstOrFail();

        $pengumuman->penerima()->updateExistingPivot($user->id_user, [
            'dibaca' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ditandai sudah dibaca',
        ]);
    }

    public function index()
    {
        $pengumuman = Pengumuman::with([
                'pembuat:id_user,nama_lengkap',
                'penerima:id_user,nama_lengkap',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengumuman,
        ]);
    }

    public function cariUser(Request $request)
    {
        $q = $request->query('q');

        $users = User::where(function ($query) use ($q) {
                $query->where('nama_lengkap', 'LIKE', "%{$q}%")
                      ->orWhere('nomor_induk', 'LIKE', "%{$q}%");
            })
            ->select('id_user', 'nama_lengkap', 'nomor_induk', 'role')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'isi_pesan' => 'required|string',
            'tipe' => 'required|in:info,penting,darurat',
            'target' => 'required|in:semua,pengunjung,staff,individu',
            'user_ids' => 'required_if:target,individu|array',
            'user_ids.*' => 'exists:users,id_user',
            'tanggal_berakhir' => 'nullable|date',
        ]);

        $pengumuman = Pengumuman::create([
            'judul' => $validated['judul'],
            'isi_pesan' => $validated['isi_pesan'],
            'tipe' => $validated['tipe'],
            'target' => $validated['target'],
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
            'dibuat_oleh' => $request->user()->id_user,
            'status' => 'aktif',
        ]);

        if ($validated['target'] === 'individu' && !empty($validated['user_ids'])) {
            $pengumuman->penerima()->sync($validated['user_ids']);
        }

        $pengumuman->load('penerima');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:150',
            'isi_pesan' => 'sometimes|required|string',
            'tipe' => 'sometimes|required|in:info,penting,darurat',
            'target' => 'sometimes|required|in:semua,pengunjung,staff,individu',
            'status' => 'sometimes|in:aktif,nonaktif',
            'user_ids' => 'required_if:target,individu|array',
            'user_ids.*' => 'exists:users,id_user',
            'tanggal_berakhir' => 'nullable|date',
        ]);

        $pengumuman->update($validated);

        $targetAkhir = $pengumuman->target;

        if ($targetAkhir === 'individu' && !empty($validated['user_ids'])) {
            $pengumuman->penerima()->sync($validated['user_ids']);
        }

        $pengumuman->load('penerima');

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui',
            'data' => $pengumuman,
        ]);
    }

    public function toggleStatus($id)
    {
        $pengumuman = Pengumuman::where('id', $id)->firstOrFail();

        $pengumuman->status = $pengumuman->status === 'aktif' ? 'nonaktif' : 'aktif';
        $pengumuman->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'data' => $pengumuman,
        ]);
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::where('id', $id)->firstOrFail();

        $pengumuman->penerima()->detach();

        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus',
        ]);
    }
}
