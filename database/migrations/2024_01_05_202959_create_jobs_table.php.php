<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\SoftDeletes; 

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id_job');
            $table->string('name_job', 50);
            $table->text('description');
            $table->string('num_caf', 50)->nullable();
            $table->date('start_date');
            $table->string('before_picture', 250)->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('id_job_status');
            $table->unsignedBigInteger('id_job_priority');
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_communication_type');
            $table->unsignedBigInteger('id_establishment');
            $table->unsignedBigInteger('id_type_maintenance');
            $table->timestamps();

            //Relacion con la tabla job_status
            $table->foreign('id_job_status')->references('id_job_status')->on('job_statuses') 
                ->cascadeOnUpdate() // Actualización en cascada
                ->restrictOnDelete(); // Las eliminaciones están restringidas

            //Relacion con la tabla job_priorities
            $table->foreign('id_job_priority')->references('id_job_priority')->on('job_priorities') 
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //Relacion con la tabla clients
            $table->foreign('id_client')->references('id_client')->on('clients') 
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //Relacion con la tabla communication_types
            $table->foreign('id_communication_type')->references('id_communication_type')->on('communication_types') 
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //Relacion con la tabla establishments
            $table->foreign('id_establishment')->references('id_establishment')->on('establishments') 
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //Relacion con la tabla type_maintenance
            $table->foreign('id_type_maintenance')->references('id_type_maintenance')->on('type_maintenances') 
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
