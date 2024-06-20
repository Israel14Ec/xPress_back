<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class type_maintenance extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA type_maintenance
    protected $table = "type_maintenances";
    protected $primaryKey = 'id_type_maintenance';
     //Campos de la tabla
     protected $fillable = [
        'name',
        'description'
    ];

    //Relación con job
    public function jobs(): HasMany {
        return $this->hasMany(Job::class, 'id_type_maintenance');
    }
}
