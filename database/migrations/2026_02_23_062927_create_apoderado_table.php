<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apoderado', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombres', 100);
            $table->string('apellido_p', 50);
            $table->string('apellido_m', 50);
            $table->string('correo', 100)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apoderado');
    }
};
