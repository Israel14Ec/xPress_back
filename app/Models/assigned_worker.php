<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class assigned_worker extends Pivot
{
    use HasFactory;

    public $timestamps = true;
    protected $table = "assigned_workers"; //Modelo de la tabla
    protected $primaryKey = 'id_assigned_worker'; // PK
    
    protected $fillable = [
        'id_user',
        'id_work_order',
    ];
    
}
