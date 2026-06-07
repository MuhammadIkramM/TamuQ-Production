<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;

    protected $fillable = [
        'nama_lengkap',
        'nomor_induk',
        'email',
        'prodi',
        'role',
        'password_hash', 
        'status_akun',   
        'foto_profil',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function fotoProfil(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                if (str_contains($value, 'http')) {
                    return $value;
                }
                return asset('storage/' . $value);
            }
        );
    }
}
