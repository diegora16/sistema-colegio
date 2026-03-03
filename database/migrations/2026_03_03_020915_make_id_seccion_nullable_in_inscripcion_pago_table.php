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
        Schema::table('inscripcion_pago', function (Blueprint $table) {
            $table->foreignId('id_seccion')
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('inscripcion_pago', function (Blueprint $table) {
            $table->foreignId('id_seccion')
                  ->nullable(false)
                  ->change();
        });
    }
};
