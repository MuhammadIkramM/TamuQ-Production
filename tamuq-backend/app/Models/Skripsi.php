<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skripsi extends Model
{
    use HasFactory;

    protected $table = 'skripsi';
    protected $primaryKey = 'id_skripsi';

    protected $fillable = [
        'user_id',
        'foto',
        'judul',
        'abstrak',
        'nama',
        'nim',
        'prodi',
        'tahun_selesai',
        'file_skripsi',
        'file_preview_skripsi',
        'status',
        'keterangan',
    ];

    protected $appends = ['foto_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;
        if (str_contains($this->foto, 'http')) {
            return $this->foto;
        }
        return asset('storage/' . $this->foto);
    }
}
