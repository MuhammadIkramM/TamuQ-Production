<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surats';

    protected $fillable = [
        'user_id',
        'jenis',
        'nama_lengkap',
        'nomor_induk',
        'prodi',
        'status',
        'jabatan',
        'alamat',
        'no_hp',
        'judul_buku',
        'file_pendukung',
        'file_balasan',
        'no_surat',
    ];

    protected $appends = ['file_pendukung_url', 'file_balasan_url'];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id', 'id_user');
    }

    public function getFilePendukungUrlAttribute()
    {
        if ($this->file_pendukung) {
            return url('storage/' . $this->file_pendukung);
        }
        return null;
    }

    public function getFileBalasanUrlAttribute()
    {
        if ($this->file_balasan) {
            return url('storage/' . $this->file_balasan);
        }
        return null;
    }
}
