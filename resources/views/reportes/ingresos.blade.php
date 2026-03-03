@extends('layouts.app')

@section('title', 'Reporte — Ingresos por Año')
@section('page-title', 'Ingresos por Año')
@section('breadcrumb', 'Reportes / Ingresos por Año')

@section('page-actions')
    @if ($anio && $totalGeneral > 0)
        <a href="{{ route('reportes.ingresos.pdf') }}?anio={{ $anio->id }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-file-pdf text-xs"></i>
            Exportar PDF
        </a>
    @endif
@endsection

@section('content')

    {{-- Selector de año --}}
    <form method="GET" class="mb-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Año académico:</label>
        <select name="anio" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 bg-white focus:outline-none focus:border-primary cursor-pointer">
            @foreach ($anios as $a)
                <option value="{{ $a->id }}" {{ $anio?->id == $a->id ? 'selected' : '' }}>
                    {{ $a->nombre }}
                    @if ($a->estado === 'activo')
                        (activo)
                    @endif
                </option>
            @endforeach
        </select>
        @if (!$anio)
            <p class="text-sm text-amber-600">No hay años académicos disponibles.</p>
        @endif
    </form>

    @if ($anio)

        {{-- Tarjeta resumen --}}
        <div class="mb-5 bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total cobrado en {{ $anio->nombre }}</p>
                <p class="text-3xl font-bold text-primary mt-1">S/ {{ number_format($totalGeneral, 2) }}</p>
            </div>
            <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-sack-dollar text-primary text-2xl"></i>
            </div>
        </div>

        {{-- Tabla de ingresos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-sm text-gray-500">
                    Ingresos registrados mes a mes
                    <span class="text-gray-400">— Año {{ $anio->nombre }}</span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Mes</th>
                            <th class="text-right px-6 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total cobrado</th>
                            <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider w-1/2">Barra</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        @php $maxMes = collect($ingresosPorMes)->max('total') ?: 1; @endphp

                        @foreach ($ingresosPorMes as $fila)
                            <tr class="hover:bg-gray-50/60 transition-colors {{ $fila['total'] == 0 ? 'opacity-40' : '' }}">
                                <td class="px-6 py-3.5 font-medium text-gray-700">{{ $fila['mes'] }}</td>
                                <td class="px-6 py-3.5 text-right font-mono font-semibold {{ $fila['total'] > 0 ? 'text-gray-800' : 'text-gray-400' }}">
                                    S/ {{ number_format($fila['total'], 2) }}
                                </td>
                                <td class="px-6 py-3.5">
                                    @if ($fila['total'] > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="h-full bg-primary rounded-full transition-all"
                                                     style="width: {{ number_format(($fila['total'] / $maxMes) * 100, 1) }}%">
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400 w-10 text-right">
                                                {{ number_format(($totalGeneral > 0 ? ($fila['total'] / $totalGeneral) * 100 : 0), 1) }}%
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr class="bg-primary/5 border-t-2 border-primary/20">
                            <td class="px-6 py-4 font-bold text-gray-800 text-sm uppercase tracking-wide">Total</td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-primary text-base">
                                S/ {{ number_format($totalGeneral, 2) }}
                            </td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

    @endif

@endsection
