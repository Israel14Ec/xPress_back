<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class equipment_assigned extends Model
{
    use HasFactory;

    protected $table = "equipment_assigneds"; //Modelo de la tabla

    protected $primaryKey = 'id_equipment_assigned'; // PK
    
    protected $fillable = [
        'is_delivered',
        'eviction_completed',
        'id_construction_equipment',
        'id_work_order',
    ];
    public $timestamps = true;

}
