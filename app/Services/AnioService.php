<?php

namespace App\Services;

use App\Models\AnioAcademico;
use App\Models\Grado;
use App\Models\NivelEducativo;

class AnioService
{
    /**
     * Estructura estándar del sistema educativo peruano.
     */
    private static array $estructura = [
        'Inicial'    => ['3 Años', '4 Años', '5 Años'],
        'Primaria'   => ['1°', '2°', '3°', '4°', '5°', '6°'],
        'Secundaria' => ['1°', '2°', '3°', '4°', '5°'],
    ];

    /**
     * Crea (o activa) el año académico indicado con sus niveles y grados estándar.
     * Marca todos los demás años como 'cerrado'.
     */
    public static function crearAnioConNiveles(int $año): AnioAcademico
    {
        // Cerrar todos los años activos que no sean el año indicado
        AnioAcademico::where('nombre', '!=', (string) $año)
            ->where('estado', 'activo')
            ->update(['estado' => 'cerrado']);

        // Crear o recuperar el año y asegurarse de que esté activo
        $anio = AnioAcademico::firstOrCreate(
            ['nombre' => (string) $año],
            ['estado' => 'activo']
        );

        if ($anio->estado !== 'activo') {
            $anio->update(['estado' => 'activo']);
        }

        // Crear niveles y grados estándar si no existen para este año
        foreach (self::$estructura as $nivelNombre => $grados) {
            $nivel = NivelEducativo::firstOrCreate(
                ['nombre' => $nivelNombre, 'id_año' => $anio->id],
                ['precio' => 0.00]
            );

            foreach ($grados as $gradoNombre) {
                Grado::firstOrCreate([
                    'nombre'       => $gradoNombre,
                    'id_educativo' => $nivel->id,
                ]);
            }
        }

        return $anio;
    }
}
