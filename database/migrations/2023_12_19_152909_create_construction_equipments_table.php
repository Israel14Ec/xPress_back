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
        Schema::create('construction_equipments', function (Blueprint $table) {
            $table->bigIncrements('id_construction_equipment');
            $table->string('name_equipment', 50);
            $table->string('description', 300)->nullable();
            $table->decimal('unit_value', $precision = 8, $scale = 2);
            $table->string('image', 250)->nullable();
            $table->unsignedBigInteger('id_status_equipment');
            $table->unsignedBigInteger('id_type_equipment');
            $table->timestamps();

            // Relaciones con las tablas de status_equipments y type_equipments
            $table->foreign('id_status_equipment')->references('id_status_equipment')->on('status_equipments')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_type_equipment')->references('id_type_equipment')->on('type_equipments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('construction_equipments');
    }
};
