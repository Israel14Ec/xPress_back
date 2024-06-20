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
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id_material');
            $table->string('name_material',50);
            $table->string('description', 300)->nullable(); 
            $table->string('image', 250)->nullable(); 
            $table->decimal('unit_value', $precision = 8, $scale = 2);
            $table->integer('stock');
            $table->decimal('total_value', $precision = 8, $scale = 2);
            $table->unsignedBigInteger('id_magnitude_value');
            $table->timestamps();

            //Relación con la tabla magnitude_values
            $table->foreign('id_magnitude_value')->references('id_magnitude_value')->on('magnitude_values')
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
        Schema::dropIfExists('materials');
    }
};
