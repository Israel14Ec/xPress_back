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
        Schema::create('department_assigneds', function (Blueprint $table) {
            $table->bigIncrements('id_department_assigned');
            $table->unsignedBigInteger('id_department');
            $table->unsignedBigInteger('id_job');
            $table->timestamps();

            //Relaciones con su tabla padre 
            $table->foreign('id_department')->references('id_department')->on('departments')->onDelete('cascade');
            $table->foreign('id_job')->references('id_job')->on('jobs')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_assigneds');
    }
};
