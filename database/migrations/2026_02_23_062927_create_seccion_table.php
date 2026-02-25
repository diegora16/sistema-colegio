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
        Schema::create('seccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_educativo')->constrained('nivel_educativo')->cascadeOnDelete();
            $table->foreignId('id_grado')->constrained('grado')->cascadeOnDelete();
            $table->string('nombre', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seccion');
    }
};
