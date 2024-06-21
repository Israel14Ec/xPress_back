<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class work_orders extends Model
{
    use HasFactory;

    protected $table = "work_orders"; //Modelo de la tabla

    protected $primaryKey = 'id_work_order'; // PK
    
    protected $fillable = [
        'instructions',
        'assigned_date',
        'end_date',
        'hour_job',
        'after_picture',
        'is_complete',
        'id_job',
        'id_order_statuses'
    ];
    
    // Mutador para establecer el assigned_date en formato 'Y-m-d'
    public function setAssignedDateAttribute($value)
    {
        $this->attributes['assigned_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    // Accesor para obtener el assigned_date en formato 'd/m/Y'
    public function getAssignedDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    // Mutador para establecer el end_date en formato 'Y-m-d'
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    // Accesor para obtener el end_date en formato 'd/m/Y'
    public function getEndDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
    
    

    protected $casts = [
        'after_picture' => 'array', 
    ];

    //Relación de n con la tabla jobs
    public function job() : BelongsTo{
        return $this->belongsTo(job::class, 'id_job');//Relación de 1 a job
    }

    //Relación de 1 con la tabla work_orders
    public function workOrderStatus() : BelongsTo {
        return $this->belongsTo(work_order_statuses::class, 'id_order_statuses');
    }

    //Relación de n con la tabla assigned_worker, con tabla intermedia
    public function workers() {
        return $this->belongsToMany(User::class, 'assigned_workers', 'id_work_order', 'id_user')->withTimestamps();
    }

    //Relacion de n a m con la tabla materials, con tabla intermedia
    public function materialAssigned() {
        return $this->belongsToMany(material::class, 'material_assigneds', 'id_work_order', 'id_material');
    }

    //Relacion de n a m con la tabla construction_equipments, con tabla intermedia
     public function equipmentAssigned() {
        return $this->belongsToMany(construction_equipment::class, 'equipment_assigneds', 'id_work_order', 'id_construction_equipment');
    }

}
