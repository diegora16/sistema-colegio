@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Inicio / Dashboard')

@section('content')

    {{-- Alerta si no hay año activo --}}
    @unless($anioActivo)
        <div class="mb-6 flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
            <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>
            </div>
            <div>
                <p class="text-amber-800 font-semibold text-sm">No hay un año académico activo</p>
                <p class="text-amber-700 text-xs mt-1">
                    Ve a
                    <a href="{{ route('configuracion.anios.index') }}"
                       class="underline font-semibold hover:text-amber-900">Configuración → Año Académico</a>
                    para crear y activar el año escolar.
                </p>
            </div>
        </div>
    @endunless

    @if($anioActivo)

    {{-- ── Tarjetas de métricas ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Alumnos matriculados --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-graduate text-primary text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500">Alumnos Matriculados</p>
                <p class="text-2xl font-bold text-gray-800 leading-tight">{{ $alumnosMatriculados }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Año {{ $anioActivo->nombre }}</p>
            </div>
        </div>

        {{-- Ingresos del mes --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-money-bill-trend-up text-blue-500 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500">Ingresos del Mes</p>
                <p class="text-2xl font-bold text-gray-800 leading-tight">S/ {{ number_format($ingresosMes, 2) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ now()->locale('es')->isoFormat('MMMM YYYY') }}</p>
            </div>
        </div>

        {{-- Alumnos con deuda --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500">Alumnos con Deuda</p>
                <p class="text-2xl font-bold text-gray-800 leading-tight">{{ $alumnosConDeuda }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Pagos pendientes o parciales</p>
            </div>
        </div>

        {{-- Total acumulado del año --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-coins text-amber-500 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500">Total {{ $anioActivo->nombre }}</p>
                <p class="text-2xl font-bold text-gray-800 leading-tight">S/ {{ number_format($ingresosTotales, 2) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Ingresos acumulados</p>
            </div>
        </div>

    </div>

    {{-- ── Gráficos ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">

        {{-- Gráfico barras: Ingresos por mes (2/3) --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-5">
                <span class="w-5 h-5 rounded bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-chart-bar text-primary text-[10px]"></i>
                </span>
                Ingresos por Mes — {{ $anioActivo->nombre }}
            </h2>
            @if(collect($ingresosPorMes)->sum('total') > 0)
                <div class="relative h-52">
                    <canvas id="chartIngresos"></canvas>
                </div>
            @else
                <div class="h-52 flex flex-col items-center justify-center text-gray-300 gap-2">
                    <i class="fa-solid fa-chart-bar text-5xl"></i>
                    <p class="text-sm text-gray-400">Sin pagos registrados aún.</p>
                </div>
            @endif
        </div>

        {{-- Gráfico doughnut: Alumnos por nivel (1/3) --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-5">
                <span class="w-5 h-5 rounded bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-primary text-[10px]"></i>
                </span>
                Alumnos por Nivel
            </h2>
            @if($alumnosPorNivel->isNotEmpty())
                <div class="relative h-52">
                    <canvas id="chartNiveles"></canvas>
                </div>
            @else
                <div class="h-52 flex flex-col items-center justify-center text-gray-300 gap-2">
                    <i class="fa-solid fa-chart-pie text-5xl"></i>
                    <p class="text-sm text-gray-400">Sin alumnos registrados.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ── Últimos pagos registrados ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left text-primary text-[10px]"></i>
                </span>
                Últimos Pagos Registrados
            </h2>
            <a href="{{ route('pagos.index') }}"
               class="text-xs font-medium text-primary hover:text-primary-dark transition-colors">
                Ver todos →
            </a>
        </div>

        @if($ultimosPagos->isEmpty())
            <div class="px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-2 text-gray-300">
                    <i class="fa-solid fa-receipt text-4xl"></i>
                    <p class="text-sm text-gray-400">No hay pagos registrados aún.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Concepto</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Comprobante</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($ultimosPagos as $pago)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-800 text-sm">
                                        {{ $pago->inscripcionPago->alumno->nombre_completo }}
                                    </div>
                                    <div class="text-gray-400 text-xs font-mono">
                                        {{ $pago->inscripcionPago->alumno->dni }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-medium text-gray-700">
                                        {{ $pago->inscripcionPago->tipoPago->nombre }}
                                    </span>
                                    @if($pago->inscripcionPago->mes)
                                        <span class="text-xs text-gray-400 block">{{ $pago->inscripcionPago->mes->nombre }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs font-semibold text-green-700">
                                    S/ {{ number_format($pago->aporte, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ $pago->fecha->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ asset('storage/' . $pago->foto) }}" target="_blank"
                                       class="inline-block group">
                                        <img src="{{ asset('storage/' . $pago->foto) }}"
                                             alt="Comprobante"
                                             class="h-8 w-8 object-cover rounded-lg border border-gray-200 group-hover:border-primary transition-colors">
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('pagos.show', $pago->inscripcionPago) }}"
                                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

    @endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    @if($anioActivo && (collect($ingresosPorMes)->sum('total') > 0 || $alumnosPorNivel->isNotEmpty()))

    const PRIMARY       = '#07863f';
    const PRIMARY_LIGHT = 'rgba(7, 134, 63, 0.12)';
    const PALETTE = ['#07863f', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#10B981'];

    Chart.defaults.font.family = "'Poppins', sans-serif";

    // ── Barras: Ingresos por mes ────────────────────────────────────────────
    const ctxIngresos = document.getElementById('chartIngresos');
    if (ctxIngresos) {
        const datosIngresos = {{ Js::from($ingresosPorMes) }};
        new Chart(ctxIngresos, {
            type: 'bar',
            data: {
                labels: datosIngresos.map(d => d.mes),
                datasets: [{
                    label: 'Ingresos (S/)',
                    data: datosIngresos.map(d => d.total),
                    backgroundColor: PRIMARY_LIGHT,
                    borderColor: PRIMARY,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' S/ ' + ctx.parsed.y.toLocaleString('es-PE', {
                                minimumFractionDigits: 2, maximumFractionDigits: 2
                            })
                        }
                    }
                },
                scales: {
                    x: {
                        grid:  { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af' }
                    },
                    y: {
                        grid:   { color: '#f3f4f6' },
                        border: { dash: [3, 3] },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: v => 'S/ ' + v.toLocaleString('es-PE')
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ── Doughnut: Alumnos por nivel ─────────────────────────────────────────
    const ctxNiveles = document.getElementById('chartNiveles');
    if (ctxNiveles) {
        const datosNiveles = {{ Js::from($alumnosPorNivel->map(fn($n) => ['nombre' => $n->nombre, 'total' => $n->alumnos_count])) }};
        new Chart(ctxNiveles, {
            type: 'doughnut',
            data: {
                labels: datosNiveles.map(n => n.nombre),
                datasets: [{
                    data: datosNiveles.map(n => n.total),
                    backgroundColor: datosNiveles.map((_, i) => PALETTE[i % PALETTE.length]),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            font: { size: 11 },
                            color: '#6b7280',
                            padding: 14
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed + ' alumno' + (ctx.parsed !== 1 ? 's' : '')
                        }
                    }
                }
            }
        });
    }

    @endif
})();
</script>
@endpush
