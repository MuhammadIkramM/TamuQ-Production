<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;

    protected $table = 'qr_tokens';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'token',
        'status',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
