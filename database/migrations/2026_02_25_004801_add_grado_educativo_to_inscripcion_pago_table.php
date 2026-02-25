<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcion_pago', function (Blueprint $table) {
            $table->foreignId('id_grado')
                  ->nullable()
                  ->after('id_seccion')
                  ->constrained('grado')
                  ->nullOnDelete();

            $table->foreignId('id_educativo')
                  ->nullable()
                  ->after('id_grado')
                  ->constrained('nivel_educativo')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inscripcion_pago', function (Blueprint $table) {
            $table->dropForeign(['id_grado']);
            $table->dropForeign(['id_educativo']);
            $table->dropColumn(['id_grado', 'id_educativo']);
        });
    }
};
