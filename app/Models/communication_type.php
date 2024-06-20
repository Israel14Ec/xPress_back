<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class communication_type extends Model
{
    use HasFactory;
    protected $table = "communication_types"; //tabla
    protected $primaryKey = 'id_communication_type'; //PK
    protected $fillable = [
        'name_communication',
        'description',
    ];

    public function jobs(): HasMany {
        return $this->hasMany(Job::class, 'id_communication_type');
    }    
}
