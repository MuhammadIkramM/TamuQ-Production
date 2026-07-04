<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkripsiRequest;
use App\Http\Requests\UpdateSkripsiRequest;
use App\Models\Skripsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SkripsiController extends Controller
{
    /**
     * List semua skripsi, urutkan terbaru dulu.
     * Support query string ?q= untuk search di kolom judul/nama/nim.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Skripsi::query()
            ->select('id_skripsi', 'judul', 'nama', 'nim', 'prodi', 'tahun_selesai')
            ->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nim', 'LIKE', "%{$search}%");
            });
        }

        $skripsi = $query->get();

        return response()->json([
            'success' => true,
            'data' => $skripsi,
        ]);
    }

    /**
     * Simpan record skripsi baru beserta upload file.
     */
    public function store(StoreSkripsiRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Upload file ke disk local, folder skripsi_files/
        $filePath = $request->file('file_skripsi')->store('skripsi_files', 'local');
        $validated['file_skripsi'] = $filePath;

        $skripsi = Skripsi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil ditambahkan.',
            'data' => $skripsi,
        ], 201);
    }

    /**
     * Update record skripsi. File hanya diubah jika ada upload baru.
     */
    public function update(UpdateSkripsiRequest $request, $id): JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->firstOrFail();

        $validated = $request->validated();

        if ($request->hasFile('file_skripsi')) {
            // Hapus file lama dari disk local jika exists
            if ($skripsi->file_skripsi && Storage::disk('local')->exists($skripsi->file_skripsi)) {
                Storage::disk('local')->delete($skripsi->file_skripsi);
            }

            // Simpan file baru
            $filePath = $request->file('file_skripsi')->store('skripsi_files', 'local');
            $validated['file_skripsi'] = $filePath;
        } else {
            // Jangan ubah kolom file_skripsi jika tidak ada file baru
            unset($validated['file_skripsi']);
        }

        $skripsi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil diperbarui.',
            'data' => $skripsi,
        ]);
    }

    /**
     * Hapus record skripsi beserta file dari disk.
     */
    public function destroy($id): JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->firstOrFail();

        // Hapus file dari disk local jika exists
        if ($skripsi->file_skripsi && Storage::disk('local')->exists($skripsi->file_skripsi)) {
            Storage::disk('local')->delete($skripsi->file_skripsi);
        }

        $skripsi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data skripsi berhasil dihapus.',
        ]);
    }

    /**
     * Preview file skripsi inline di browser/iframe.
     */
    public function preview($id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi || !$skripsi->file_skripsi || !Storage::disk('local')->exists($skripsi->file_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        return Storage::disk('local')->response($skripsi->file_skripsi);
    }

    /**
     * Download file skripsi dengan nama file yang user-friendly.
     */
    public function download($id): StreamedResponse|JsonResponse
    {
        $skripsi = Skripsi::where('id_skripsi', $id)->first();

        if (!$skripsi || !$skripsi->file_skripsi || !Storage::disk('local')->exists($skripsi->file_skripsi)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        // Nama file download: kombinasi judul dan nim + .pdf
        $namaFile = $skripsi->judul . ' - ' . $skripsi->nim . '.pdf';

        return Storage::disk('local')->download($skripsi->file_skripsi, $namaFile);
    }
}
