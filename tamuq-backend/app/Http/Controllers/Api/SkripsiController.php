<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjukanSkripsiRequest;
use App\Http\Requests\StoreSkripsiRequest;
use App\Http\Requests\UpdateSkripsiRequest;
use App\Models\Skripsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SkripsiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtolower($user->role ?? '');
        $isStaff = in_array($role, ['staff', 'superadmin']);

        $query = Skripsi::query()
            ->select(
                'id_skripsi', 'user_id', 'foto', 'judul', 'abstrak',
                'nama', 'nim', 'prodi', 'tahun_selesai',
                'file_skripsi', 'file_preview_skripsi',
                'status', 'keterangan', 'created_at', 'updated_at'
            )
            ->orderBy('created_at', 'desc');

        if ($isStaff) {
            if ($request->filled('status')) {
                $statuses = array_map('trim', explode(',', $request->query('status')));
                $allowed = ['menunggu', 'disetujui', 'ditolak'];
                $statuses = array_intersect($statuses, $allowed);
                if (!empty($statuses)) {
                    $query->whereIn('status', $statuses);
                }
            }
        } else {
            $query->where('status', 'disetujui');
        }

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nim', 'LIKE', "%{$search}%");
            });
        }

        $skripsi = $query->get();

        if (!$isStaff) {
            $skripsi->makeHidden(['keterangan']);
        }

        return response()->json([
            'success' => true,
            'data' => $skripsi,
        ]);
    }

    public function store(StoreSkripsiRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('skripsi_foto', 'public');
        }

        $validated['file_skripsi'] = $request->file('file_skripsi')->store('skripsi_files', 'local');

        if ($request->hasFile('file_preview_skripsi')) {
            $validated['file_preview_skripsi'] = $request->file('file_preview_skripsi')->store('skripsi_preview_files', 'local');
        }

        $validated['status'] = 'disetujui';
        $validated['user_id'] = null;

        $skripsi = Skripsi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil ditambahkan.',
            'data' => $skripsi,
        ], 201);
    }

    public function ajukan(AjukanSkripsiRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('skripsi_foto', 'public');
        }

        $validated['file_skripsi'] = $request->file('file_skripsi')->store('skripsi_files', 'local');

        if ($request->hasFile('file_preview_skripsi')) {
            $validated['file_preview_skripsi'] = $request->file('file_preview_skripsi')->store('skripsi_preview_files', 'local');
        }

        $validated['user_id'] = $user->id_user;
        $validated['nama'] = $user->nama_lengkap;
        $validated['nim'] = $user->nomor_induk;
        $validated['prodi'] = $user->prodi ?? 'PTIK';
        $validated['status'] = 'menunggu';
        $validated['keterangan'] = '-';

        $skripsi = Skripsi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan skripsi berhasil dikirim.',
            'data' => $skripsi,
        ], 201);
    }

    public function update(UpdateSkripsiRequest $request, $id): JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->firstOrFail();

        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            if ($skripsi->foto && Storage::disk('public')->exists($skripsi->foto)) {
                Storage::disk('public')->delete($skripsi->foto);
            }
            $validated['foto'] = $request->file('foto')->store('skripsi_foto', 'public');
        } else {
            unset($validated['foto']);
        }

        if ($request->hasFile('file_skripsi')) {
            if ($skripsi->file_skripsi && Storage::disk('local')->exists($skripsi->file_skripsi)) {
                Storage::disk('local')->delete($skripsi->file_skripsi);
            }
            $validated['file_skripsi'] = $request->file('file_skripsi')->store('skripsi_files', 'local');
        } else {
            unset($validated['file_skripsi']);
        }

        if ($request->hasFile('file_preview_skripsi')) {
            if ($skripsi->file_preview_skripsi && Storage::disk('local')->exists($skripsi->file_preview_skripsi)) {
                Storage::disk('local')->delete($skripsi->file_preview_skripsi);
            }
            $validated['file_preview_skripsi'] = $request->file('file_preview_skripsi')->store('skripsi_preview_files', 'local');
        } else {
            unset($validated['file_preview_skripsi']);
        }

        if (!$request->has('status')) {
            unset($validated['status']);
        }
        if (!$request->has('keterangan')) {
            unset($validated['keterangan']);
        }

        $skripsi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil diperbarui.',
            'data' => $skripsi,
        ]);
    }

    public function verify(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:disetujui,ditolak',
            'keterangan' => 'required_if:status,ditolak|nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $skripsi = Skripsi::where('id_skripsi', $id)->first();
        if (!$skripsi) {
            return response()->json(['message' => 'Data skripsi tidak ditemukan.'], 404);
        }

        try {
            $keterangan = $request->keterangan;
            if ($request->status === 'disetujui' && empty(trim($keterangan ?? ''))) {
                $keterangan = 'Skripsi Diterima, Harap Cek Skripsi Di Katalog Skripsi';
            }

            $skripsi->status = $request->status;
            $skripsi->keterangan = $keterangan;
            $skripsi->save();

            return response()->json([
                'success' => true,
                'message' => 'Status skripsi berhasil diperbarui menjadi ' . $skripsi->status . '.',
                'data' => $skripsi,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal verifikasi: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->firstOrFail();

        if ($skripsi->foto && Storage::disk('public')->exists($skripsi->foto)) {
            Storage::disk('public')->delete($skripsi->foto);
        }

        if ($skripsi->file_skripsi && Storage::disk('local')->exists($skripsi->file_skripsi)) {
            Storage::disk('local')->delete($skripsi->file_skripsi);
        }

        if ($skripsi->file_preview_skripsi && Storage::disk('local')->exists($skripsi->file_preview_skripsi)) {
            Storage::disk('local')->delete($skripsi->file_preview_skripsi);
        }

        $skripsi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil dihapus.',
        ]);
    }

    private function checkFileAccess(Request $request, Skripsi $skripsi, string $type): ?JsonResponse
    {
        $user = $request->user();
        $role = strtolower($user->role ?? '');
        $isStaff = in_array($role, ['staff', 'superadmin']);

        if ($isStaff) {
            return null; 
        }

        if ($skripsi->user_id && (int)$skripsi->user_id === (int)$user->id_user) {
            return null; 
        }

        if ($type === 'preview' && $skripsi->status === 'disetujui') {
            return null; 
        }

        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Anda tidak berhak mengakses file ini.',
        ], 403);
    }

    public function preview(Request $request, $id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Data skripsi tidak ditemukan.',
            ], 404);
        }

        $denied = $this->checkFileAccess($request, $skripsi, 'full');
        if ($denied) return $denied;

        if (!$skripsi->file_skripsi || !Storage::disk('local')->exists($skripsi->file_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File skripsi tidak ditemukan di storage.',
            ], 404);
        }

        return Storage::disk('local')->response($skripsi->file_skripsi, null, [
            'Content-Type' => 'application/pdf',
        ], 'inline');
    }

    public function download(Request $request, $id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Data skripsi tidak ditemukan.',
            ], 404);
        }

        $denied = $this->checkFileAccess($request, $skripsi, 'full');
        if ($denied) return $denied;

        if (!$skripsi->file_skripsi || !Storage::disk('local')->exists($skripsi->file_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File skripsi tidak ditemukan di storage.',
            ], 404);
        }

        $namaFile = $skripsi->judul . ' - ' . $skripsi->nim . '.pdf';

        return Storage::disk('local')->download($skripsi->file_skripsi, $namaFile);
    }

    public function previewFilePreview(Request $request, $id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Data skripsi tidak ditemukan.',
            ], 404);
        }

        $denied = $this->checkFileAccess($request, $skripsi, 'preview');
        if ($denied) return $denied;

        if (!$skripsi->file_preview_skripsi || !Storage::disk('local')->exists($skripsi->file_preview_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File preview skripsi tidak ditemukan di storage.',
            ], 404);
        }

        return Storage::disk('local')->response($skripsi->file_preview_skripsi, null, [
            'Content-Type' => 'application/pdf',
        ], 'inline');
    }

    public function downloadFilePreview(Request $request, $id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi) {
            return response()->json([
                'success' => false,
                'message' => 'Data skripsi tidak ditemukan.',
            ], 404);
        }

        $denied = $this->checkFileAccess($request, $skripsi, 'preview');
        if ($denied) return $denied;

        if (!$skripsi->file_preview_skripsi || !Storage::disk('local')->exists($skripsi->file_preview_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File preview skripsi tidak ditemukan di storage.',
            ], 404);
        }

        $namaFile = $skripsi->judul . ' - ' . $skripsi->nim . ' (Preview).pdf';

        return Storage::disk('local')->download($skripsi->file_preview_skripsi, $namaFile);
    }

    public function riwayatSaya(Request $request): JsonResponse
    {
        $user = $request->user();

        $skripsi = Skripsi::where('user_id', $user->id_user)
            ->select(
                'id_skripsi', 'user_id', 'foto', 'judul', 'abstrak',
                'nama', 'nim', 'prodi', 'tahun_selesai',
                'file_skripsi', 'file_preview_skripsi',
                'status', 'keterangan', 'created_at', 'updated_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $skripsi,
        ]);
    }
}
