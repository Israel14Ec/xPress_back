<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class type_equipment extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA type_equipments
    protected $table = "type_equipments";
    protected $primaryKey = 'id_type_equipment';
     //Campos de la tabla
     protected $fillable = [
        'name_type_equipment',
        'description'
    ];

    //Relación con equipment
    public function construction_equipment(): HasMany{
        return $this->hasMany(construction_equipment::class, 'id_type_equipment'); //Relación de n a equipment
    }

}
