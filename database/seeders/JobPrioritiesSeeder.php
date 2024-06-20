<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\job_priorities;

class JobPrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        job_priorities::firstOrCreate([
            'name' => 'Desarrollo normal',
            'description' => 'El trabajo se puede cumplir con normalidad',
            'level' => 2 
        ]);

         //
         job_priorities::firstOrCreate([
            'name' => 'Emergente',
            'description' => 'El trabajo tiene la máxima prioridad para cumplirse',
            'level' => 1
        ]);
    }
}
