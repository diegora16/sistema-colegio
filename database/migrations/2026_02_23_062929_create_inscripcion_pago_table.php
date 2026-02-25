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
        Schema::create('inscripcion_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_alumno')->constrained('alumno')->restrictOnDelete();
            $table->foreignId('id_seccion')->constrained('seccion')->restrictOnDelete();
            $table->foreignId('id_año')->constrained('anio_academico')->restrictOnDelete();
            $table->foreignId('id_tipo_pago')->constrained('tipo_pago')->restrictOnDelete();
            $table->foreignId('id_mes')->nullable()->constrained('mes')->nullOnDelete();
            $table->decimal('costo_total', 8, 2);
            $table->date('fecha_registro');
            $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion_pago');
    }
};
