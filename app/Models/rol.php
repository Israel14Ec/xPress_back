<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rol extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA rol
    protected $table = "rols";
    
    protected $primaryKey = 'id_rol'; // Define la clave primaria
    //Campos de la tabla
    protected $fillable = [
        'name_rol',
        'description'
    ];

    //Relaciones de la tabla
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'id_rol'); //Relacion de 1
    }
}
