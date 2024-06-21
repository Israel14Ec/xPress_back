<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class job_priorities extends Model
{
    use HasFactory;

    protected $table="job_priorities";
    protected $primaryKey="id_job_priority";
    protected $fillable = [
        'name',
        'description',
        'level'
    ];

    public function jobs(): HasMany {
        return $this->hasMany(job::class, 'id_job_priority');
    }
    
}
