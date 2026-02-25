<?php

namespace Database\Seeders;

use App\Models\TipoPago;
use Illuminate\Database\Seeder;

class TipoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['Matrícula', 'Mensualidad'];

        foreach ($tipos as $nombre) {
            TipoPago::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
