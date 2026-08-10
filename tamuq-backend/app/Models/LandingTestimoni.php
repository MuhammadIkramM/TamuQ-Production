<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingTestimoni extends Model
{
    use HasFactory;

    protected $table = 'landing_testimoni';

    protected $fillable = [
        'foto',
        'nama',
        'jabatan',
        'kutipan',
        'urutan',
        'status',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')->orderBy('urutan');
    }
}
