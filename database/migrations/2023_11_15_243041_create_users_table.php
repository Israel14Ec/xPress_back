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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_user');
            $table->string('name',50);
            $table->string('last_name', 50);
            $table->string('image', 250)->nullable(); //Acepta valores nulos
            $table->string('phone_number', 10)->nullable()->unique(); //Acepta valores nulos
            $table->string('email', 50)->unique(); //El valor de este campo es único
            $table->string('password', 100);
            $table->string('token', 60)->nullable(); //Token para resetar password
            $table->unsignedBigInteger('id_department'); //Campo a relacionar con departamento
            $table->unsignedBigInteger('id_rol')->default(4); //Campo a relacionar con rol, no asignado
            $table->timestamps();

            //Relación con la tabla departments
            $table->foreign('id_department')->references('id_department')->on('departments')
                ->cascadeOnUpdate() //Actualización en cascada
                ->restrictOnDelete();//Las eliminaciones están restringidas
            
            //Relación con la tabla rol
            $table->foreign('id_rol')->references('id_rol')->on('rols')
                ->cascadeOnUpdate() //Actualización en cascada
                ->restrictOnDelete(); //Las eliminaciones están restringidas
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
