<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class user extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;
  
    //MODELO DE LA TABLA user
    protected $table = "users"; //Se relaciona con la tabla users en la DB
    protected $primaryKey = 'id_user'; // Define la clave primaria
    protected $fillable = [ //Campos de la tabla que se pueden asignar en masa
      'name',
      'last_name',
      'phone_number',
      'email',
      'password',
      'id_department',
      'id_rol'
    ];

    //Relacion de la tabla departamento
    public function department(): belongsTo{
        return $this->belongsTo(Department::class, 'id_department'); //Relacion de 1 a departamento
    }
    //Relacion de la tabla 
    public function rol(): belongsTo {
       return $this->belongsTo(Rol::class, 'id_rol'); //Relacion de 1 a usuario
    }

    // Para la relación muchos a muchos con WorkOrder
    public function assignedWorkOrders() {
        return $this->belongsToMany( work_orders::class, 'assigned_workers', 'id_user', 'id_work_order')->withTimestamps();
    }

}
