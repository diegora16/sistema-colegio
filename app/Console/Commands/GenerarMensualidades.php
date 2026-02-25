<?php

namespace App\Console\Commands;

use App\Models\AnioAcademico;
use App\Models\InscripcionPago;
use App\Models\Mes;
use App\Models\TipoPago;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerarMensualidades extends Command
{
    protected $signature = 'mensualidades:generar
                            {--mes= : Número de mes (1-12); por defecto el mes actual}
                            {--año= : ID del año académico; por defecto el año activo}';

    protected $description = 'Genera las mensualidades del mes indicado para todos los alumnos con matrícula pagada';

    public function handle(): int
    {
        $mesNumero = $this->option('mes') ? (int) $this->option('mes') : now()->month;

        $anioActivo = $this->option('año')
            ? AnioAcademico::find((int) $this->option('año'))
            : AnioAcademico::where('estado', 'activo')->first();

        if (!$anioActivo) {
            $this->warn('No hay año académico activo. No se generaron mensualidades.');
            return 0;
        }

        $tipoMatricula   = TipoPago::where('nombre', 'Matrícula')->first();
        $tipoMensualidad = TipoPago::where('nombre', 'Mensualidad')->first();
        $mesRecord       = Mes::where('numero', $mesNumero)->first();

        if (!$tipoMatricula || !$tipoMensualidad) {
            $this->error('No se encontraron los tipos de pago (Matrícula / Mensualidad). Ejecuta los seeders.');
            return 1;
        }

        if (!$mesRecord) {
            $this->warn("El mes {$mesNumero} no existe en la tabla de meses (fuera del año escolar).");
            return 0;
        }

        // Obtener todas las matrículas pagadas del año activo
        $matriculas = InscripcionPago::with('alumno.nivelEducativo')
            ->where('id_año', $anioActivo->id)
            ->where('id_tipo_pago', $tipoMatricula->id)
            ->where('estado', 'pagado')
            ->get();

        $generadas  = 0;
        $omitidas   = 0;

        foreach ($matriculas as $matricula) {
            $yaExiste = InscripcionPago::where('id_alumno', $matricula->id_alumno)
                ->where('id_año', $anioActivo->id)
                ->where('id_tipo_pago', $tipoMensualidad->id)
                ->where('id_mes', $mesRecord->id)
                ->exists();

            if ($yaExiste) {
                $omitidas++;
                continue;
            }

            $precio = (float) ($matricula->alumno->nivelEducativo?->precio ?? 0);

            InscripcionPago::create([
                'id_alumno'      => $matricula->id_alumno,
                'id_seccion'     => $matricula->id_seccion,
                'id_año'         => $anioActivo->id,
                'id_tipo_pago'   => $tipoMensualidad->id,
                'id_mes'         => $mesRecord->id,
                'costo_total'    => $precio,
                'fecha_registro' => Carbon::now()->startOfMonth()->toDateString(),
                'estado'         => 'pendiente',
            ]);

            $generadas++;
        }

        $this->info("Mensualidades generadas: {$generadas} | Omitidas (ya existían): {$omitidas}");
        $this->info("Mes: {$mesRecord->nombre} | Año: {$anioActivo->nombre}");

        return 0;
    }
}
