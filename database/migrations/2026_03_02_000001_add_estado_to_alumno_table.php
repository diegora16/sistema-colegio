<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            // activo   = matriculado / en curso
            // retirado = se fue del colegio (puede volver)
            // egresado = terminó 5° Secundaria
            $table->enum('estado', ['activo', 'retirado', 'egresado'])
                  ->default('activo')
                  ->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
