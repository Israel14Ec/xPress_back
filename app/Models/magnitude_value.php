<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class magnitude_value extends Model
{
    use HasFactory;

    protected $table="magnitude_values";
    protected $primaryKey="id_magnitude_value";

    protected $fillable = [
        'value',
        'id_magnitude'
    ];

     //Relación con la tabla magnitude
     public function magnitude(): BelongsTo{
        return $this->belongsTo(magnitude::class, 'id_magnitude'); //Relacion de 1 a magnitudes
    }
    //Relación con la tabla material
    public function material(): HasMany{
        return $this->hasMany(material::class, 'id_material');//Relación de n a material
    }

}
