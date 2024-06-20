<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class work_order_statuses extends Model
{
    use HasFactory;

    protected $table="work_order_statuses";
    protected $primaryKey="id_order_statuses";

    protected $fillable = [
        'name',
        'description',
        'step'
    ];

    //Relación con la tabla work_orders
    public function workOrders(): HasMany {
        return $this->hasMany(work_orders::class, 'id_work_order');
    }

}
