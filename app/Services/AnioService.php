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
     * Mapa de progresión de grado: nivel actual + grado actual → siguiente nivel + grado.
     * null = el alumno egresa (Secundaria 5°).
     */
    private static array $progresion = [
        'Inicial' => [
            '3 Años' => ['nivel' => 'Inicial',    'grado' => '4 Años'],
            '4 Años' => ['nivel' => 'Inicial',    'grado' => '5 Años'],
            '5 Años' => ['nivel' => 'Primaria',   'grado' => '1°'],
        ],
        'Primaria' => [
            '1°' => ['nivel' => 'Primaria',   'grado' => '2°'],
            '2°' => ['nivel' => 'Primaria',   'grado' => '3°'],
            '3°' => ['nivel' => 'Primaria',   'grado' => '4°'],
            '4°' => ['nivel' => 'Primaria',   'grado' => '5°'],
            '5°' => ['nivel' => 'Primaria',   'grado' => '6°'],
            '6°' => ['nivel' => 'Secundaria', 'grado' => '1°'],
        ],
        'Secundaria' => [
            '1°' => ['nivel' => 'Secundaria', 'grado' => '2°'],
            '2°' => ['nivel' => 'Secundaria', 'grado' => '3°'],
            '3°' => ['nivel' => 'Secundaria', 'grado' => '4°'],
            '4°' => ['nivel' => 'Secundaria', 'grado' => '5°'],
            '5°' => null,
        ],
    ];

    /**
     * Devuelve el siguiente nivel y grado dados el nivel y grado actuales.
     * Retorna null si el alumno egresa (Secundaria 5°).
     */
    public static function siguienteGrado(string $nivelNombre, string $gradoNombre): ?array
    {
        return self::$progresion[$nivelNombre][$gradoNombre] ?? null;
    }

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
