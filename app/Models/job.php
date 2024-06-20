<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class job extends Model
{
    use SoftDeletes; //Eliminado Logico

    use HasFactory;
    protected $table = "jobs"; //tabla
    protected $primaryKey = 'id_job'; //PK
    protected $fillable = [
        'name_job',
        'description',
        'num_caf',
        'start_date',
        'before_picture',
        'id_job_status',
        'id_job_priority',
        'id_client',
        'id_communication_type',
        'id_establishment',
        'id_type_maintenance'
    ];

    public function  jobStatus(): BelongsTo {
        return $this->belongsTo(job_status::class, 'id_job_status');//Relación de 1 a job_status
    }

    public function  jobPriorities(): BelongsTo {
        return $this->belongsTo(job_priorities::class, 'id_job_priority');//Relación de 1 a job_priorities
    }

    public function clients(): BelongsTo {
        return $this->belongsTo(client::class, 'id_client');//Relación de 1 a clients
    }

    public function establishment(): BelongsTo {
        return $this->belongsTo(establishment::class, 'id_establishment');//Relación de 1 a establishment
    }

    public function communicationType (): BelongsTo {
        return $this->belongsTo(communication_type::class, 'id_communication_type');//Relación de 1 a communication_type
    }

    public function typeMaintenance (): BelongsTo {
        return $this->belongsTo(type_maintenance::class, 'id_type_maintenance');//Relación de 1 a type_maintenance
    }

    //Relación de n a n con department usando la tabla intermedia
    public function departments() {
        return $this->belongsToMany(Department::class, 'department_assigneds', 'id_job', 'id_department');
    }

    //Relación de n a work_orders
    public function workOrders () : HasMany {
        return $this->hasMany(work_orders::class, 'id_job');
    }
     
}
