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
        Schema::create('gmail_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('id_user')->constrained('users')->onDelete('cascade'); // Referencia a la clave primaria de la tabla 'users'
            $table->string('access_token');
            $table->string('refresh_token');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gmail_tokens');
    }
};
