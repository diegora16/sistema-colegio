@extends('layouts.app')

@section('title', 'Reporte — Pagos por Alumno')
@section('page-title', 'Pagos por Alumno')
@section('breadcrumb', 'Reportes / Pagos por Alumno')

@section('page-actions')
    @if ($alumno)
        <a href="{{ route('reportes.pagos_alumno.pdf') }}?alumno_id={{ $alumno->id }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-file-pdf text-xs"></i>
            Exportar PDF
        </a>
    @endif
@endsection

@section('content')

    {{-- Buscador --}}
    <form method="GET" action="{{ route('reportes.pagos_alumno') }}" class="mb-5">
        @if ($alumno)
            <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
        @endif
        <div class="flex gap-2">
            <input type="text" name="q" value="{{ $busqueda }}"
                   placeholder="Buscar por nombre, apellidos o DNI…"
                   class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-primary bg-white shadow-sm">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Buscar
            </button>
            @if ($busqueda || $alumno)
                <a href="{{ route('reportes.pagos_alumno') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    {{-- Lista de resultados de búsqueda --}}
    @if ($busqueda && !$alumno && $alumnos->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50 mb-5">
            <div class="px-5 py-3 bg-gray-50 rounded-t-xl">
                <p class="text-xs text-gray-500 font-medium">{{ $alumnos->count() }} resultado(s) para "{{ $busqueda }}"</p>
            </div>
            @foreach ($alumnos as $a)
                <a href="{{ route('reportes.pagos_alumno') }}?alumno_id={{ $a->id }}"
                   class="flex items-center px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <span class="font-semibold text-gray-800 text-sm">{{ $a->nombre_completo }}</span>
                        <span class="ml-2 text-xs text-gray-400 font-mono">{{ $a->dni }}</span>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $a->nivelEducativo?->nombre }}
                        @if ($a->grado) · {{ $a->grado->nombre }} @endif
                        @if ($a->seccion) · Secc. {{ $a->seccion->nombre }} @endif
                    </span>
                    <i class="fa-solid fa-chevron-right text-gray-300 text-xs ml-3"></i>
                </a>
            @endforeach
        </div>
    @elseif ($busqueda && !$alumno && $alumnos->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-10 text-center mb-5">
            <i class="fa-solid fa-magnifying-glass text-3xl text-gray-200 mb-2"></i>
            <p class="text-sm text-gray-400">No se encontraron alumnos con "{{ $busqueda }}".</p>
        </div>
    @endif

    {{-- Historial del alumno seleccionado --}}
    @if ($alumno)

        {{-- Tarjeta info alumno --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user-graduate text-primary text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-base leading-tight">{{ $alumno->nombre_completo }}</h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">DNI: {{ $alumno->dni }}</p>
                </div>
                <div class="text-right text-xs text-gray-500 space-y-0.5">
                    <p><span class="text-gray-400">Nivel:</span> {{ $alumno->nivelEducativo?->nombre ?? '—' }}</p>
                    <p><span class="text-gray-400">Grado:</span> {{ $alumno->grado?->nombre ?? '—' }} · Secc. {{ $alumno->seccion?->nombre ?? '—' }}</p>
                    <p><span class="text-gray-400">Apoderado:</span> {{ $alumno->apoderado?->nombre_completo ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Tabla historial --}}
        @php
            $totalCosto  = $inscripciones->sum('costo_total');
            $totalPagado = $inscripciones->sum(fn($i) => $i->pagos->sum('aporte'));
            $totalSaldo  = $totalCosto - $totalPagado;
            $badges = [
                'pendiente' => 'bg-amber-100 text-amber-700',
                'parcial'   => 'bg-blue-100 text-blue-700',
                'pagado'    => 'bg-green-100 text-green-700',
            ];
            $labels = ['pendiente' => 'Pendiente', 'parcial' => 'Parcial', 'pagado' => 'Pagado'];
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-sm text-gray-500">
                    {{ $inscripciones->count() }} {{ $inscripciones->count() === 1 ? 'registro' : 'registros' }}
                    @if ($anioActivo)
                        <span class="text-gray-400">— Año {{ $anioActivo->nombre }}</span>
                    @endif
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Mes</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Costo</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pagado</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Saldo</th>
                            <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($inscripciones as $ins)
                            @php
                                $pagado = (float) $ins->pagos->sum('aporte');
                                $saldo  = (float) $ins->costo_total - $pagado;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-3.5 text-gray-700 text-xs font-medium">{{ $ins->tipoPago?->nombre ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $ins->mes?->nombre ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs text-gray-700">S/ {{ number_format($ins->costo_total, 2) }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs text-green-700">S/ {{ number_format($pagado, 2) }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs {{ $saldo > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    S/ {{ number_format($saldo, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $badges[$ins->estado] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ $labels[$ins->estado] ?? $ins->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-sm text-gray-400">No hay registros de pago para este alumno en el año activo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($inscripciones->isNotEmpty())
                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-xs font-bold text-gray-600 uppercase tracking-wide">Totales</td>
                                <td class="px-5 py-3 text-right font-mono text-xs font-bold text-gray-700">S/ {{ number_format($totalCosto, 2) }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs font-bold text-green-700">S/ {{ number_format($totalPagado, 2) }}</td>
                                <td class="px-5 py-3 text-right font-mono text-xs font-bold {{ $totalSaldo > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    S/ {{ number_format($totalSaldo, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

        </div>

    @elseif (!$busqueda)
        {{-- Estado vacío inicial --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-14 text-center">
            <i class="fa-solid fa-file-invoice-dollar text-4xl text-gray-200 mb-3"></i>
            <p class="text-sm text-gray-400">Busca un alumno por nombre o DNI para ver su historial de pagos.</p>
        </div>
    @endif

@endsection
