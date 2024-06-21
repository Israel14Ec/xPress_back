<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class department extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA department
    protected $table = "departments";

    protected $primaryKey = 'id_department'; // Define la clave primaria
    protected $fillable = [
        'name_department',
        'description'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(user::class, 'id_department'); //Relación de n a usuario
    }

    //Relacion de n a n con job, usando tabla intermedia
    public function jobs(): BelongsToMany {
        return $this->belongsToMany(job::class, 'department_assigneds', 'id_department', 'id_job'); 
    }
    

}
