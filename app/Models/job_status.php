<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class job_status extends Model
{
    use HasFactory;
    protected $table = "job_statuses"; //tabla
    protected $primaryKey = 'id_job_status'; //PK
    protected $fillable = [
        'name',
        'description',
        'color',
        'step'
    ];

    public function job(): HasMany{
        return $this->hasMany(job::class, 'id_job_status'); //Relacion de n a job
    }
}
