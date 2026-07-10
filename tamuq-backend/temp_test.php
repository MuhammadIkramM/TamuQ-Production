<?php
$controller = new \App\Http\Controllers\Api\SkripsiController();
// create dummy skripsi and file
$skripsi = \App\Models\Skripsi::create([
    'nim' => '1234', 'nama' => 'Test', 'judul' => 'ok', 'prodi' => 'PTIK', 'tahun_selesai' => 2023, 'file_skripsi' => 'skripsi_files/test2.pdf'
]);
if (!\Illuminate\Support\Facades\Storage::disk('local')->exists('skripsi_files/test2.pdf')) {
    \Illuminate\Support\Facades\Storage::disk('local')->put('skripsi_files/test2.pdf', 'dummy pdf content');
}
$response = $controller->preview($skripsi->id_skripsi);
echo "CLASS: " . get_class($response) . PHP_EOL;
echo "STATUS: " . $response->getStatusCode() . PHP_EOL;
echo "CONTENT_TYPE: " . $response->headers->get('Content-Type') . PHP_EOL;
