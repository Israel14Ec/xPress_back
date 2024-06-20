<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class reportMaterial extends Model
{
    use HasFactory;

    protected $table = "report_materials"; //Modelo de la tabla

    protected $primaryKey = 'id_report_material'; // PK
    
    protected $fillable = [
        'amount_left_over',
        'eviction_completed',
        'id_material_assigned',
    ];
    public $timestamps = true;

    //Relacion de 1 a material assigned
    public function materialAssigned(): BelongsTo {
        return $this->belongsTo(material_assigned::class, 'id_material_assigned');//Relación de 1 a clients
    }

}
