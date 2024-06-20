<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class material_assigned extends Model
{
    use HasFactory;

    protected $table = "material_assigneds"; //Modelo de la tabla

    protected $primaryKey = 'id_material_assigned'; // PK
    
    protected $fillable = [
        'amount',
        'delivered_amount',
        'is_delivered',
        'id_material',
        'id_work_order'
    ];
    public $timestamps = true;

    //Relacion con tabla report_material
    public function reportMaterial(): HasOne {
        return $this->hasOne(reportMaterial::class, 'id_material_assigned');
    }
}
