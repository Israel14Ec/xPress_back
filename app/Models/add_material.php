<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class add_material extends Model
{
    use HasFactory;

    //MODELO DE LA TABLA add_material
    protected $table = "add_materials";
    protected $primaryKey = 'id_add_material';

    //Campos
    protected $fillable = [
        'stock',
        'description',
        'id_material',
    ];

    //Relación con la tabla materials
    public function material(): BelongsTo{
        return $this->belongsTo(Material::class, 'id_material'); //Relacion de n a departamento
    }
}
