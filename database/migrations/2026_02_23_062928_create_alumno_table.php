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
        Schema::create('alumno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_apoderado')->constrained('apoderado')->restrictOnDelete();
            $table->foreignId('id_educativo')->constrained('nivel_educativo')->restrictOnDelete();
            $table->foreignId('id_grado')->constrained('grado')->restrictOnDelete();
            $table->foreignId('id_seccion')->constrained('seccion')->restrictOnDelete();
            $table->string('dni', 8)->unique();
            $table->string('nombres', 100);
            $table->string('apellido_p', 50);
            $table->string('apellido_m', 50);
            $table->date('fecha_nacimiento');
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
        Schema::dropIfExists('alumno');
    }
};
