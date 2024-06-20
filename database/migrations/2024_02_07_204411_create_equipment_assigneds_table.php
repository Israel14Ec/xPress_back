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
        Schema::create('equipment_assigneds', function (Blueprint $table) {
            $table->bigIncrements('id_equipment_assigned');
            $table->boolean('is_delivered')->default(false); //Esta entregado el equipo para ese trabajo
            $table->boolean('eviction_completed')->default(false); //Esta entregado el equipo para ese trabajo
            $table->unsignedBigInteger('id_construction_equipment');
            $table->unsignedBigInteger('id_work_order');
            $table->timestamps();

            //Relación con la tabla construction_equipment
            $table->foreign('id_construction_equipment')->references('id_construction_equipment')->on('construction_equipments')->onDelete('cascade');
            
            //Relación con la tabla work_orders
            $table->foreign('id_work_order')->references('id_work_order')->on('work_orders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_assigneds');
    }
};
