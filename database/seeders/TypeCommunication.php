<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\communication_type;

class TypeCommunication extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        communication_type::firstOrCreate([
            'name_communication' => 'Correo electrónico',
            'description' => 'La información del trabajo llego mediante correos electrónicos',
        ]);
        communication_type::firstOrCreate([
            'name_communication' => 'Mensaje',
            'description' => 'la información del trabajo llego mediante mensajes de texto',
        ]);
    }
}
