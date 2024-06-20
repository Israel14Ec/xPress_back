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
        Schema::create('type_equipments', function (Blueprint $table) {
            $table->bigIncrements('id_type_equipment');
            $table->string('name_type_equipment',50);
            $table->string('description',300);
            $table->timestamps();
            
        });
        //Inserción de los datos
        DB::table('type_equipments')->insert([
            ['name_type_equipment' => 'Herramienta manual', 'description' => 'Son herramientas que se utilizan para ejecutar de manera más apropiada tareas de reparación', 'created_at' => now(), 'updated_at' => now()],
            ['name_type_equipment' => 'Maquinaria', 'description' => 'Se incluyen un grupo de máquinas utilizadas en actividades de construcción', 'created_at' => now(), 'updated_at' => now()],
            ['name_type_equipment' => 'Equipos de seguridad', 'description' => 'Evitan el contacto directo con los peligros de ambientes riesgosos', 'created_at' => now(), 'updated_at' => now()],
        ]);

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_equipments');
    }
};
