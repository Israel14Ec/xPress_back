<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class establishment extends Model
{
    use HasFactory;

    //Modelo de la tabla establishment
    protected $table="establishments";
    protected $primaryKey="id_establishment";
    protected $fillable = [
        'name_establishment',
        'description',
        'location'
    ];

    //Convierte en array a la tabla location
    protected $casts = [
        'location' => 'array',
    ];
    
    public function jobs(): HasMany {
        return $this->hasMany(Job::class, 'id_establishment');
    }    
}
