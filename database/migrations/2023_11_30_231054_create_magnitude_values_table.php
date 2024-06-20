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
        Schema::create('magnitude_values', function (Blueprint $table) {
            $table->bigIncrements('id_magnitude_value');
            $table->string('value', 100);
            $table->unsignedBigInteger('id_magnitude');
            $table->timestamps();

            //Relación con la tabla magnitude_value
            $table->foreign('id_magnitude')->references('id_magnitude')->on('magnitudes')
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
        Schema::dropIfExists('magnitude_values');
    }
};
