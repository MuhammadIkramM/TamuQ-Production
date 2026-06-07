<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPengembalian extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pengembalian';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'id_peminjaman',
        'tanggal_pinjam',
        'tanggal_kembali',
        'id_user',
        'id_buku'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function buku() {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function peminjaman() {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }
}
