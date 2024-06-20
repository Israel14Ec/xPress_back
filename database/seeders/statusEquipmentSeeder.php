<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\status_equipment;

class statusEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        status_equipment::firstOrCreate([
            'name_status_equipment' => 'Activo',
            'description' => 'El equipo se puede usar',
            'color' => '#22C55E'
        ]);
        status_equipment::firstOrCreate([
            'name_status_equipment' => 'Inactivo',
            'description' => 'El equipo no puede ser usado',
            'color' => '#22C55E'
        ]);
        status_equipment::firstOrCreate([
            'name_status_equipment' => 'Disponible',
            'description' => 'El equipo puede ser asignado para los trabajo',
            'color' => '#22C55E'
        ]);
        status_equipment::firstOrCreate([
            'name_status_equipment' => 'Ocupado',
            'description' => 'El equipo no puede ser asignado, esta en uso',
            'color' => '#EAB308'
        ]);
        status_equipment::firstOrCreate([
            'name_status_equipment' => 'Desalojo',
            'description' => 'Está en proceso de ser retirado el equipo',
            'color' => '#F3F4F6'
        ]);
    }
}

