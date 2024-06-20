<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class magnitude extends Model
{
    use HasFactory;

    protected $table="magnitudes";
    protected $primaryKey="id_magnitude";

    protected $fillable = [
        'name',
        'symbol'
    ];

    //Relación con la tabla magnitude_value
    public function magnitude_value(): HasMany{
        return $this->hasMany(magnitude_value::class, 'id_magnitude_value');//Relación de n a add_material
    }

}
