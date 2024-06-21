<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class client extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $table = "clients"; //tabla
    protected $primaryKey = 'id_client'; //PK
    protected $fillable = [
        'name_client',
        'description',
    ];

    public function jobs(): HasMany {
        return $this->hasMany(job::class, 'id_client');
    }
}
