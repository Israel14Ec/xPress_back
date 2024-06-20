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
        Schema::create('assigned_workers', function (Blueprint $table) {
            $table->bigIncrements('id_assigned_worker');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_work_order');
            $table->timestamps();
            
            //Relación con la tabla users
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            
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
        Schema::dropIfExists('assigned_workers');
    }
};
