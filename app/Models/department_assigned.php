<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class department_assigned extends Pivot
{
    use HasFactory;

    protected $table = 'department_assigneds';
    protected $primaryKey = 'id_department_assigned'; // Define la clave primaria
    protected $fillable = [
        'id_department',
        'id_job'
    ];
    public $timestamps = true;
}
