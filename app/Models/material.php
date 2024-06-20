<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class material extends Model
{
    use HasFactory;

    //Modelo de la tabla material
    protected $table="materials";
    
    protected $primaryKey="id_material";

    protected $fillable = [
        'name_material',
        'description',
        'image',
        'unit_value',
        'stock',
        'total_value',
        'id_magnitude_value'
    ];
  
    //Relación con add_material
    public function add_material(): HasMany{
        return $this->hasMany(add_material::class, 'id_add_material');//Relación de n a add_material
    }

    //Relacion de n a work_orders, tabla intermedia
    public function workOrders(): BelongsToMany {
        return $this->belongsToMany(work_orders::class, 'material_assigneds', 'id_material', 'id_work_order');
    }

   // Relación con la tabla magnitudes_value
    public function magnitude_value(): BelongsTo{
        return $this->belongsTo(magnitude_value::class, 'id_magnitude_value'); // Relación de n a 1
    }
}
