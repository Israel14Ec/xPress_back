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
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id_department');
            $table->string('name_department', 50);
            $table->string('description', 300);
            $table->timestamps();
        });

        //Insercción de roles definidos
        DB::table('departments')->insert([
            ['name_department' => 'Administración', 'description' => 'Encargado del gestión de personal y recursos', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
