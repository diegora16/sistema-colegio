<?php

namespace App\Http\Controllers;

use App\Models\AnioAcademico;
use App\Models\InscripcionPago;
use App\Models\NivelEducativo;
use App\Models\Pago;
use App\Models\TipoPago;

class DashboardController extends Controller
{
    public function index()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        if (!$anioActivo) {
            return view('dashboard', [
                'anioActivo'          => null,
                'alumnosMatriculados' => 0,
                'ingresosMes'         => 0,
                'alumnosConDeuda'     => 0,
                'ingresosTotales'     => 0,
                'ingresosPorMes'      => [],
                'alumnosPorNivel'     => collect(),
                'ultimosPagos'        => collect(),
            ]);
        }

        $tipoMatricula   = TipoPago::where('nombre', 'Matrícula')->first();
        $tipoMensualidad = TipoPago::where('nombre', 'Mensualidad')->first();

        // ── Card 1: Alumnos con matrícula pagada ─────────────────────────────
        $alumnosMatriculados = $tipoMatricula
            ? InscripcionPago::where('id_año', $anioActivo->id)
                ->where('id_tipo_pago', $tipoMatricula->id)
                ->where('estado', 'pagado')
                ->count()
            : 0;

        // ── Card 2: Ingresos del mes actual ──────────────────────────────────
        $ingresosMes = (float) Pago::whereHas(
            'inscripcionPago',
            fn($q) => $q->where('id_año', $anioActivo->id)
        )
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('aporte');

        // ── Card 3: Alumnos con algún pago pendiente / parcial (matrícula o mensualidad)
        $alumnosConDeuda = InscripcionPago::where('id_año', $anioActivo->id)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->distinct('id_alumno')
            ->count('id_alumno');

        // ── Card 4: Ingresos totales acumulados del año ──────────────────────
        $ingresosTotales = (float) Pago::whereHas(
            'inscripcionPago',
            fn($q) => $q->where('id_año', $anioActivo->id)
        )->sum('aporte');

        // ── Gráfico 1: Ingresos por mes (1 sola query agrupada) ──────────────
        $pagosPorMes = Pago::selectRaw('MONTH(fecha) as mes_num, SUM(aporte) as total')
            ->whereHas('inscripcionPago', fn($q) => $q->where('id_año', $anioActivo->id))
            ->groupByRaw('MONTH(fecha)')
            ->pluck('total', 'mes_num')
            ->mapWithKeys(fn($t, $m) => [(int) $m => round((float) $t, 2)]);

        $mesesTodos = [
            1 => 'Enero', 2 => 'Febrero',  3 => 'Marzo',     4 => 'Abril',
            5 => 'Mayo',  6 => 'Junio',    7 => 'Julio',     8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $ingresosPorMes = collect($mesesTodos)->map(fn($nombre, $num) => [
            'mes'   => $nombre,
            'total' => $pagosPorMes->get($num, 0),
        ])->values()->toArray();

        // ── Gráfico 2: Alumnos activos por nivel educativo ───────────────────
        $alumnosPorNivel = NivelEducativo::where('id_año', $anioActivo->id)
            ->withCount(['alumnos' => fn($q) => $q->where('estado', 'activo')])
            ->orderBy('nombre')
            ->get()
            ->filter(fn($n) => $n->alumnos_count > 0)
            ->values();

        // ── Tabla: Últimos 8 pagos ────────────────────────────────────────────
        $ultimosPagos = Pago::with([
            'inscripcionPago.alumno',
            'inscripcionPago.tipoPago',
            'inscripcionPago.mes',
        ])
            ->whereHas('inscripcionPago', fn($q) => $q->where('id_año', $anioActivo->id))
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'anioActivo',
            'alumnosMatriculados',
            'ingresosMes',
            'alumnosConDeuda',
            'ingresosTotales',
            'ingresosPorMes',
            'alumnosPorNivel',
            'ultimosPagos',
        ));
    }
}
