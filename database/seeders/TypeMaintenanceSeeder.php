<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\type_maintenance;

class TypeMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserta un estados predeterminados
        type_maintenance::firstOrCreate([
            'name' => 'Correctivo',
            'description' => 'Consiste en reparar la avería una vez que se ha producido',
        ]);

        type_maintenance::firstOrCreate([
            'name' => 'Preventivo',
            'description' => 'Tareas de mantenimiento que tienen como objetivo la reducción riesgos, previniendo fallos',
        ]);
    }
}
