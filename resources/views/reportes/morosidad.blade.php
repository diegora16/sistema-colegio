@extends('layouts.app')

@section('title', 'Reporte — Morosidad')
@section('page-title', 'Morosidad')
@section('breadcrumb', 'Reportes / Morosidad')

@section('page-actions')
    @if ($anioActivo && $inscripciones->isNotEmpty())
        <a href="{{ route('reportes.morosidad.pdf') }}?{{ http_build_query(request()->only(['nivel', 'mes'])) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-file-pdf text-xs"></i>
            Exportar PDF
        </a>
    @endif
@endsection

@section('content')

    {{-- Sin año activo --}}
    @if (!$anioActivo)
        <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. Activa uno en <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Configuración → Años</a>.</p>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="mb-4 flex flex-col sm:flex-row gap-3 flex-wrap">

        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-wrap">

            {{-- Nivel --}}
            <select name="nivel" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 bg-white focus:outline-none focus:border-primary cursor-pointer">
                <option value="">Todos los niveles</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ request('nivel') == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>

            {{-- Mes --}}
            <select name="mes" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 bg-white focus:outline-none focus:border-primary cursor-pointer">
                <option value="">Todos los meses</option>
                @foreach ($meses as $mes)
                    <option value="{{ $mes->id }}" {{ request('mes') == $mes->id ? 'selected' : '' }}>
                        {{ $mes->nombre }}
                    </option>
                @endforeach
            </select>

            {{-- Limpiar --}}
            @if (request()->anyFilled(['nivel', 'mes']))
                <a href="{{ route('reportes.morosidad') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-500 hover:text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Limpiar
                </a>
            @endif

        </form>

    </div>

    {{-- Tarjetas resumen --}}
    @if ($anioActivo)
        @php
            $totalDeuda  = $inscripciones->sum('costo_total');
            $totalPagado = $inscripciones->sum('pagos_sum_aporte');
            $totalSaldo  = $totalDeuda - $totalPagado;
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Alumnos morosos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $inscripciones->count() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">registros pendientes / parciales</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Deuda total</p>
                <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($totalDeuda, 2) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">costo total de cuotas pendientes</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Saldo pendiente</p>
                <p class="text-2xl font-bold text-red-600">S/ {{ number_format($totalSaldo, 2) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">monto aún no cobrado</p>
            </div>
        </div>
    @endif

    {{-- Tabla --}}
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
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">N°</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Grado / Secc.</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Mes</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Costo</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pagado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($inscripciones as $i => $ins)
                        @php
                            $pagado = (float) ($ins->pagos_sum_aporte ?? 0);
                            $saldo  = (float) $ins->costo_total - $pagado;
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800 text-sm">{{ $ins->alumno->nombre_completo }}</div>
                                <div class="text-gray-400 text-xs font-mono">{{ $ins->alumno->dni }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                @if ($ins->tipoPago?->nombre === 'Matrícula')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Matrícula</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Mensualidad</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $ins->nivelEducativo?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $ins->grado?->nombre ?? '—' }}
                                @if ($ins->seccion)
                                    <span class="text-gray-400">/ {{ $ins->seccion->nombre }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $ins->mes?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs text-gray-700">S/ {{ number_format($ins->costo_total, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs text-green-700">S/ {{ number_format($pagado, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs font-semibold text-red-600">S/ {{ number_format($saldo, 2) }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($ins->estado === 'pendiente')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pendiente</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Parcial</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-circle-check text-4xl text-green-300"></i>
                                    <p class="text-sm text-gray-400">No hay alumnos con cuotas pendientes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
