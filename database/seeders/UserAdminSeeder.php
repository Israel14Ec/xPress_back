<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\user;
use Illuminate\Support\Facades\Hash;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserta un estados predeterminados
        user::firstOrCreate([
            'name' => 'user',
            'last_name' => 'admin',
            'phone_number' => '9999999999',
            'email' => 'correo@correo.com',
            'password' => Hash::make('correo@correo.com'), // Encripta la contraseña
            'id_department' => 1,
            'id_rol' => 1
        ]);
    }
}
