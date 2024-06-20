<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rols', function (Blueprint $table) {
            $table->bigIncrements('id_rol');
            $table->string('name_rol',50); 
            $table->string('description',100); 
            $table->timestamps();
        });

        //Insercción de roles definidos
        DB::table('rols')->insert([
            ['name_rol' => 'Administrador', 'description' => 'Tiene todos los permisos', 'created_at' => now(), 'updated_at' => now()],
            ['name_rol' => 'Jefe de departamento', 'description' => 'Tiene algunos permisos elevados', 'created_at' => now(), 'updated_at' => now()],
            ['name_rol' => 'Empleado', 'description' => 'Es un usuario básico', 'created_at' => now(), 'updated_at' => now()],
            ['name_rol' => 'No asignado', 'description' => 'Rol por defecto para usuarios no asignados', 'created_at' => now(), 'updated_at' => now()],
            ['name_rol' => 'Acceso Removido', 'description' => 'Usuario sin acceso a la aplicación', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rols');
    }
};
