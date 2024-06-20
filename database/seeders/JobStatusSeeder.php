<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\job_status;

class JobStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserta estados predeterminados
        job_status::firstOrCreate([
            'name' => 'Rechazado',
            'description' => 'El trabajo no se realizará.',
            'color' => '#EF4444' 
        ]);
        job_status::firstOrCreate([
            'name' => 'Aceptado',
            'description' => 'El trabajo ha sido aceptado.',
            'color' => '#22C55E',
            'step' => 1
        ]);
        job_status::firstOrCreate([
            'name' => 'Asignado departamento',
            'description' => 'Al trabajo se le asigno uno o varios departamentos',
            'color' => '#FACC15',
            'step' => 2 
        ]);
        job_status::firstOrCreate([
            'name' => 'Asignado recursos',
            'description' => 'Al trabajo se le asigno recursos (materiales, equipos y personal).',
            'color' => '#F6CA06',
            'step' => 3 
        ]);
        job_status::firstOrCreate([
            'name' => 'En progreso',
            'description' => 'El trabajo esta en progreso',
            'color' => '#3B82F6',
            'step' => 4
        ]);
        job_status::firstOrCreate([
            'name' => 'Finalizado',
            'description' => 'El trabajo ha sido finalizado con exito.',
            'color' => '#000000',
            'step' => 5
        ]);

    }
}
