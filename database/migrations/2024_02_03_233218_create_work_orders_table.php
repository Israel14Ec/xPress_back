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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->bigIncrements('id_work_order');
            $table->text('instructions');
            $table->date('assigned_date');
            $table->date('end_date');
            $table->decimal('hour_job', $precision = 8, $scale = 2)->nullable();
            $table->text('after_picture')->nullable(); //Json para guardar
            $table->unsignedBigInteger('id_job');
            $table->unsignedBigInteger('id_order_statuses');
            $table->timestamps();

            //Relación con la tabla jobs
            $table->foreign('id_job')->references('id_job')->on('jobs') 
                    ->cascadeOnUpdate() // Actualización en cascada
                    ->restrictOnDelete(); // Las eliminaciones están restringidas
            
            //Relación con la tabla work_order_statuses
            $table->foreign('id_order_statuses')->references('id_order_statuses')->on('work_order_statuses')
                ->cascadeOnUpdate() // Actualización en cascada
                ->restrictOnDelete(); // Las eliminaciones están restringidas
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
};
