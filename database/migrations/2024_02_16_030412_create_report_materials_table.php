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
        Schema::create('report_materials', function (Blueprint $table) {
            $table->bigIncrements('id_report_material');
            $table->integer('amount_left_over');
            $table->boolean('eviction_completed')->default(false);
            $table->unsignedBigInteger('id_material_assigned');
            $table->timestamps();

            //Relación con la tabla material_assigned
            $table->foreign('id_material_assigned')->references('id_material_assigned')->on('material_assigneds')->onDelete('cascade');
        });

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_materials');
    }
};
