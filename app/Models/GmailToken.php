<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GmailToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Definir la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(user::class, 'id_user');
    }
}
