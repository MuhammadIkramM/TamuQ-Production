<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi_pesan',
        'tipe',
        'target',
        'status',
        'tanggal_mulai',
        'tanggal_berakhir',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_berakhir' => 'datetime',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }

    public function penerima()
    {
        return $this->belongsToMany(User::class, 'pengumuman_penerima', 'pengumuman_id', 'user_id', 'id', 'id_user')
            ->withPivot('dibaca', 'id')
            ->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', now())
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')
                  ->orWhere('tanggal_berakhir', '>', now());
            });
    }
}
