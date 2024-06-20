<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class status_equipment extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA type_equipments
    protected $table = "status_equipments";
    protected $primaryKey = 'id_status_equipment';
     //Campos de la tabla
     protected $fillable = [
        'name_status_equipment',
        'description',
        'color'
    ];

    
    //Relación con equipment
    public function construction_equipment(): HasMany{
        return $this->hasMany(construction_equipment::class, 'id_status_equipment');//Relación de n a equipment
    }
}
