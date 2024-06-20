<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\work_order_statuses;

class WorkOrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        work_order_statuses::firstOrCreate([
            'name'=> 'Asignado recursos',
            'description' => 'A la ordén de trabajo se le asigno recursos (materiales, equipos y personal), por parte del jefe de departamento',
            'step' => 1
        ]);
        work_order_statuses::firstOrCreate([
            'name'=> 'En progreso',
            'description' => 'La ordén de trabajo esta en progreso',
            'step' => 2
        ]);
        work_order_statuses::firstOrCreate([
            'name'=> 'Finalizado',
            'description' => 'La ordén de trabajo se completo',
            'step' => 3
        ]);
    }

    
}
