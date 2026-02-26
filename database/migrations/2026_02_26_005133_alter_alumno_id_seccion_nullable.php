<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropForeign(['id_seccion']);
            $table->unsignedBigInteger('id_seccion')->nullable()->change();
            $table->foreign('id_seccion')->references('id')->on('seccion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alumno', function (Blueprint $table) {
            $table->dropForeign(['id_seccion']);
            $table->unsignedBigInteger('id_seccion')->nullable(false)->change();
            $table->foreign('id_seccion')->references('id')->on('seccion')->restrictOnDelete();
        });
    }
};
