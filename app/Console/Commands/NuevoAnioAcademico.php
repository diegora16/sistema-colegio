<?php

namespace App\Console\Commands;

use App\Services\AnioService;
use Illuminate\Console\Command;

class NuevoAnioAcademico extends Command
{
    protected $signature   = 'app:nuevo-anio';
    protected $description = 'Crea y activa el año académico del año calendario actual (GMT-5).';

    public function handle(): void
    {
        $año  = (int) now()->setTimezone('America/Lima')->format('Y');
        $anio = AnioService::crearAnioConNiveles($año);

        $this->info("Año académico {$anio->nombre} activado correctamente con niveles y grados.");
    }
}
