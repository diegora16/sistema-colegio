<?php

namespace Database\Seeders;

use App\Services\AnioService;
use Illuminate\Database\Seeder;

class AnioAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $año = (int) now()->setTimezone('America/Lima')->format('Y');
        AnioService::crearAnioConNiveles($año);
    }
}
