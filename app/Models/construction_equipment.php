<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class construction_equipment extends Model
{
    use HasFactory;
    
    //MODELO DE LA TABLA construction_equipment
    protected $table = "construction_equipments";
    protected $primaryKey = 'id_construction_equipment';
    //Campos de la tabla
    protected $fillable = [
        'name_equipment',
        'description',
        'unit_value',
        'total_value',
        'image',
        'id_status_equipment',
        'id_type_equipment'
    ];

    public function status_equipment(): belongsTo{
        return $this->belongsTo(status_equipment::class, 'id_status_equipment');//Relación de 1 a status
    }

    public function type_equipment(): belongsTo {
        return $this->belongsTo(type_equipment::class, 'id_type_equipment');//Relación de 1 a type
    }

    //Relacion de n con work_orders, tabla intermedia
    public function workOrders() : BelongsToMany {
        return $this->belongsToMany(work_orders::class, 'equipment_assigneds', 'id_construction_equipment', 'id_work_order');
    }
}
