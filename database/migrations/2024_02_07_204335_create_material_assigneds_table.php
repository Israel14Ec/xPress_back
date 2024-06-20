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
        Schema::create('material_assigneds', function (Blueprint $table) {
            $table->bigIncrements('id_material_assigned');
            $table->integer('amount'); //Cantidad solicitada
            $table->integer('delivered_amount')->nullable(); //Cantidad entregada
            $table->boolean('is_delivered')->default(false);
            $table->unsignedBigInteger('id_material');
            $table->unsignedBigInteger('id_work_order');
            $table->timestamps();

            //Relación con la tabla material
            $table->foreign('id_material')->references('id_material')->on('materials')->onDelete('cascade');
            
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
        Schema::dropIfExists('material_assigneds');
    }
};
