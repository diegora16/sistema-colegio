<?php

namespace Database\Seeders;

use App\Models\Mes;
use Illuminate\Database\Seeder;

class MesSeeder extends Seeder
{
    public function run(): void
    {
        $meses = [
            ['nombre' => 'Marzo',      'numero' => 3],
            ['nombre' => 'Abril',      'numero' => 4],
            ['nombre' => 'Mayo',       'numero' => 5],
            ['nombre' => 'Junio',      'numero' => 6],
            ['nombre' => 'Julio',      'numero' => 7],
            ['nombre' => 'Agosto',     'numero' => 8],
            ['nombre' => 'Septiembre', 'numero' => 9],
            ['nombre' => 'Octubre',    'numero' => 10],
            ['nombre' => 'Noviembre',  'numero' => 11],
            ['nombre' => 'Diciembre',  'numero' => 12],
        ];

        foreach ($meses as $mes) {
            Mes::firstOrCreate(['numero' => $mes['numero']], ['nombre' => $mes['nombre']]);
        }
    }
}
