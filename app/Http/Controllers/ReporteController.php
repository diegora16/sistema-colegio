<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AnioAcademico;
use App\Models\Grado;
use App\Models\InscripcionPago;
use App\Models\Mes;
use App\Models\NivelEducativo;
use App\Models\Pago;
use App\Models\Seccion;
use App\Models\TipoPago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // ── Landing ────────────────────────────────────────────────────────────────
    public function index()
    {
        return view('reportes.index');
    }

    // ── Reporte 1: Alumnos matriculados ────────────────────────────────────────
    public function alumnos(Request $request)
    {
        $anioActivo    = AnioAcademico::where('estado', 'activo')->first();
        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();

        $niveles   = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        $grados    = $request->filled('nivel')
            ? Grado::where('id_educativo', $request->nivel)->orderBy('nombre')->get()
            : collect();
        $secciones = $request->filled('grado')
            ? Seccion::where('id_grado', $request->grado)->orderBy('nombre')->get()
            : collect();

        $alumnos = Alumno::with([
            'apoderado',
            'seccion',
            'inscripcionesPago' => fn($q) => $q
                ->when($anioActivo && $tipoMatricula, fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                )
                ->with(['grado', 'nivelEducativo', 'seccion']),
        ])
            ->where('estado', 'activo')
            ->when($anioActivo && $tipoMatricula, fn($q) =>
                $q->whereHas('inscripcionesPago', fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                       ->where('estado', 'pagado')
                       ->when($request->filled('nivel'),   fn($q3) => $q3->where('id_educativo', $request->nivel))
                       ->when($request->filled('grado'),   fn($q3) => $q3->where('id_grado',     $request->grado))
                       ->when($request->filled('seccion'), fn($q3) =>
                           $q3->where(fn($q4) =>
                               $q4->where('id_seccion', $request->seccion)
                                  ->orWhere(fn($q5) =>
                                      $q5->whereNull('id_seccion')
                                         ->whereHas('alumno', fn($q6) => $q6->where('id_seccion', $request->seccion))
                                  )
                           )
                       )
                )
            )
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->get();

        return view('reportes.alumnos', compact(
            'alumnos', 'anioActivo', 'niveles', 'grados', 'secciones'
        ));
    }

    public function alumnosPdf(Request $request)
    {
        $anioActivo    = AnioAcademico::where('estado', 'activo')->first();
        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();

        $alumnos = Alumno::with([
            'apoderado',
            'seccion',
            'inscripcionesPago' => fn($q) => $q
                ->when($anioActivo && $tipoMatricula, fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                )
                ->with(['grado', 'nivelEducativo', 'seccion']),
        ])
            ->where('estado', 'activo')
            ->when($anioActivo && $tipoMatricula, fn($q) =>
                $q->whereHas('inscripcionesPago', fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                       ->where('estado', 'pagado')
                       ->when($request->filled('nivel'),   fn($q3) => $q3->where('id_educativo', $request->nivel))
                       ->when($request->filled('grado'),   fn($q3) => $q3->where('id_grado',     $request->grado))
                       ->when($request->filled('seccion'), fn($q3) =>
                           $q3->where(fn($q4) =>
                               $q4->where('id_seccion', $request->seccion)
                                  ->orWhere(fn($q5) =>
                                      $q5->whereNull('id_seccion')
                                         ->whereHas('alumno', fn($q6) => $q6->where('id_seccion', $request->seccion))
                                  )
                           )
                       )
                )
            )
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->get();

        $nivelNombre   = $request->filled('nivel')
            ? NivelEducativo::find($request->nivel)?->nombre : null;
        $gradoNombre   = $request->filled('grado')
            ? Grado::find($request->grado)?->nombre : null;
        $seccionNombre = $request->filled('seccion')
            ? Seccion::find($request->seccion)?->nombre : null;

        $pdf = Pdf::loadView('reportes.pdf.alumnos', compact(
            'alumnos', 'anioActivo', 'nivelNombre', 'gradoNombre', 'seccionNombre'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('reporte-alumnos-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Reporte 2: Morosidad ───────────────────────────────────────────────────
    public function morosidad(Request $request)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $niveles = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        $meses = Mes::orderBy('numero')->get();

        $inscripciones = InscripcionPago::with([
            'alumno',
            'nivelEducativo',
            'grado',
            'seccion',
            'tipoPago',
            'mes',
        ])
            ->withSum('pagos', 'aporte')
            ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->when($request->filled('nivel'), fn($q) => $q->where('id_educativo', $request->nivel))
            ->when($request->filled('mes'), fn($q) => $q->where('id_mes', $request->mes))
            ->orderBy('id_tipo_pago')
            ->orderBy('id_mes')
            ->get();

        return view('reportes.morosidad', compact(
            'inscripciones', 'anioActivo', 'niveles', 'meses'
        ));
    }

    public function morosidadPdf(Request $request)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $inscripciones = InscripcionPago::with([
            'alumno',
            'nivelEducativo',
            'grado',
            'seccion',
            'tipoPago',
            'mes',
        ])
            ->withSum('pagos', 'aporte')
            ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->when($request->filled('nivel'), fn($q) => $q->where('id_educativo', $request->nivel))
            ->when($request->filled('mes'), fn($q) => $q->where('id_mes', $request->mes))
            ->orderBy('id_tipo_pago')
            ->orderBy('id_mes')
            ->get();

        $nivelNombre = $request->filled('nivel')
            ? NivelEducativo::find($request->nivel)?->nombre : null;
        $mesNombre   = $request->filled('mes')
            ? Mes::find($request->mes)?->nombre : null;

        $totalDeuda  = $inscripciones->sum('costo_total');
        $totalPagado = $inscripciones->sum('pagos_sum_aporte');
        $totalSaldo  = $totalDeuda - $totalPagado;

        $pdf = Pdf::loadView('reportes.pdf.morosidad', compact(
            'inscripciones', 'anioActivo', 'nivelNombre', 'mesNombre',
            'totalDeuda', 'totalPagado', 'totalSaldo'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('reporte-morosidad-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Reporte 3: Pagos por alumno ────────────────────────────────────────────
    public function pagosPorAlumno(Request $request)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();
        $busqueda   = $request->input('q', '');
        $alumnos    = collect();
        $alumno     = null;
        $inscripciones = collect();

        if ($busqueda !== '') {
            $alumnos = Alumno::with(['nivelEducativo', 'grado', 'seccion'])
                ->when($anioActivo, fn($q) =>
                    $q->whereHas('nivelEducativo', fn($q2) => $q2->where('id_año', $anioActivo->id))
                )
                ->where(function ($q) use ($busqueda) {
                    $q->where('nombres',     'like', "%{$busqueda}%")
                      ->orWhere('apellido_p', 'like', "%{$busqueda}%")
                      ->orWhere('apellido_m', 'like', "%{$busqueda}%")
                      ->orWhere('dni',        'like', "%{$busqueda}%");
                })
                ->orderBy('apellido_p')
                ->limit(20)
                ->get();
        }

        if ($request->filled('alumno_id')) {
            $alumno = Alumno::with(['apoderado', 'nivelEducativo', 'grado', 'seccion'])
                ->findOrFail($request->alumno_id);

            $inscripciones = InscripcionPago::with(['tipoPago', 'mes', 'pagos'])
                ->where('id_alumno', $alumno->id)
                ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
                ->orderBy('id_tipo_pago')
                ->orderBy('id_mes')
                ->get();
        }

        return view('reportes.pagos_alumno', compact(
            'alumno', 'inscripciones', 'busqueda', 'alumnos', 'anioActivo'
        ));
    }

    public function pagosPorAlumnoPdf(Request $request)
    {
        $request->validate(['alumno_id' => ['required', 'exists:alumno,id']]);

        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $alumno = Alumno::with(['apoderado', 'nivelEducativo', 'grado', 'seccion'])
            ->findOrFail($request->alumno_id);

        $inscripciones = InscripcionPago::with(['tipoPago', 'mes', 'pagos', 'anioAcademico'])
            ->where('id_alumno', $alumno->id)
            ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
            ->orderBy('id_tipo_pago')
            ->orderBy('id_mes')
            ->get();

        $totalCosto  = $inscripciones->sum('costo_total');
        $totalPagado = $inscripciones->sum(fn($i) => $i->pagos->sum('aporte'));
        $totalSaldo  = $totalCosto - $totalPagado;

        $pdf = Pdf::loadView('reportes.pdf.pagos_alumno', compact(
            'alumno', 'inscripciones', 'anioActivo',
            'totalCosto', 'totalPagado', 'totalSaldo'
        ))->setPaper('a4', 'portrait');

        $nombreArchivo = 'pagos-' . str($alumno->apellido_p)->slug() . '-' . $alumno->dni . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    // ── Reporte 4: Ingresos totales por año ────────────────────────────────────
    public function ingresos(Request $request)
    {
        $anios = AnioAcademico::orderByDesc('nombre')->get();

        // Año seleccionado: el que pide el usuario, o el activo, o el primero disponible
        $anioId = $request->input('anio');
        $anio   = $anioId
            ? AnioAcademico::find($anioId)
            : (AnioAcademico::where('estado', 'activo')->first() ?? $anios->first());

        $ingresosPorMes = $this->calcularIngresosPorMes($anio);
        $totalGeneral   = array_sum(array_column($ingresosPorMes, 'total'));

        return view('reportes.ingresos', compact('anios', 'anio', 'ingresosPorMes', 'totalGeneral'));
    }

    public function ingresosPdf(Request $request)
    {
        $anios  = AnioAcademico::orderByDesc('nombre')->get();
        $anioId = $request->input('anio');
        $anio   = $anioId
            ? AnioAcademico::find($anioId)
            : (AnioAcademico::where('estado', 'activo')->first() ?? $anios->first());

        $ingresosPorMes = $this->calcularIngresosPorMes($anio);
        $totalGeneral   = array_sum(array_column($ingresosPorMes, 'total'));

        $pdf = Pdf::loadView('reportes.pdf.ingresos', compact(
            'anio', 'ingresosPorMes', 'totalGeneral'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('reporte-ingresos-' . ($anio->nombre ?? now()->year) . '.pdf');
    }

    /**
     * Agrupa los pagos recibidos del año académico indicado por mes de pago (pago.fecha).
     * Devuelve un array de 12 elementos [mes, total] para Enero–Diciembre.
     */
    private function calcularIngresosPorMes(?AnioAcademico $anio): array
    {
        $nombresMes = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',    4 => 'Abril',
            5 => 'Mayo',  6 => 'Junio',   7 => 'Julio',    8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $totalesPorMes = $anio
            ? Pago::selectRaw('MONTH(fecha) as mes_num, SUM(aporte) as total')
                ->whereHas('inscripcionPago', fn($q) => $q->where('id_año', $anio->id))
                ->groupByRaw('MONTH(fecha)')
                ->orderByRaw('MONTH(fecha)')
                ->pluck('total', 'mes_num')
            : collect();

        $resultado = [];
        for ($i = 1; $i <= 12; $i++) {
            $resultado[] = [
                'mes'   => $nombresMes[$i],
                'total' => (float) ($totalesPorMes[$i] ?? 0),
            ];
        }

        return $resultado;
    }
}
