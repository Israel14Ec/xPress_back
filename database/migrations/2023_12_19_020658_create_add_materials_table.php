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
        Schema::create('add_materials', function (Blueprint $table) {
            $table->bigIncrements('id_add_material');
            $table->integer('stock');
            $table->string('description', 300);
            $table->unsignedBigInteger('id_material');
            $table->timestamps();

            //Relación con la tabla materials
            $table->foreign('id_material')->references('id_material')->on('materials')
                ->cascadeOnUpdate() //Actualización en cascada
                ->restrictOnDelete();//Las eliminaciones están restringidas
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_materials');
    }
};
