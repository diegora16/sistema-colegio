@extends('layouts.app')

@section('title', 'Pagos')
@section('page-title', 'Pagos')
@section('breadcrumb', 'Pagos')

@section('page-actions')
    @if ($anioActivo)
        <a href="{{ route('pagos.matricular') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            Nueva Inscripción
        </a>
    @endif
@endsection

@section('content')

    @if (!$anioActivo)
        <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. Activa uno en <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Configuración → Años</a>.</p>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">

        {{-- Píldoras de estado --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            @php $estadoActual = request('estado', ''); @endphp
            @foreach (['' => 'Todos', 'pendiente' => 'Pendiente', 'parcial' => 'Parcial', 'pagado' => 'Pagado'] as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['estado' => $val]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                          {{ $estadoActual === $val
                              ? 'bg-primary text-white shadow-sm'
                              : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Filtro por tipo --}}
        <form method="GET" class="sm:ml-auto">
            <input type="hidden" name="estado" value="{{ request('estado') }}">
            <select name="tipo" onchange="this.form.submit()"
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-600 bg-white focus:outline-none focus:border-primary cursor-pointer">
                <option value="">Todos los tipos</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->id }}" {{ request('tipo') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </form>

    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">
                {{ $inscripciones->count() }} {{ $inscripciones->count() === 1 ? 'inscripción' : 'inscripciones' }}
                @if ($anioActivo)
                    <span class="text-gray-400">— Año {{ $anioActivo->nombre }}</span>
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Mes</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Costo</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pagado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($inscripciones as $inscripcion)
                        @php
                            $pagado = (float) ($inscripcion->pagos_sum_aporte ?? 0);
                            $saldo  = (float) $inscripcion->costo_total - $pagado;
                            $badges = [
                                'pendiente' => 'bg-amber-100 text-amber-700',
                                'parcial'   => 'bg-blue-100 text-blue-700',
                                'pagado'    => 'bg-green-100 text-green-700',
                            ];
                            $labels = [
                                'pendiente' => 'Pendiente',
                                'parcial'   => 'Parcial',
                                'pagado'    => 'Pagado',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800 text-sm">{{ $inscripcion->alumno->nombre_completo }}</div>
                                <div class="text-gray-400 text-xs font-mono">{{ $inscripcion->alumno->dni }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs font-medium">{{ $inscripcion->tipoPago->nombre }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $inscripcion->mes?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs text-gray-700">S/ {{ number_format($inscripcion->costo_total, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs text-green-700">S/ {{ number_format($pagado, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-xs {{ $saldo > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                S/ {{ number_format($saldo, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $badges[$inscripcion->estado] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $labels[$inscripcion->estado] ?? $inscripcion->estado }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('pagos.show', $inscripcion) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-receipt text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay inscripciones registradas.</p>
                                    @if ($anioActivo)
                                        <a href="{{ route('pagos.matricular') }}"
                                           class="mt-1 text-xs text-primary hover:text-primary-dark font-medium">
                                            Registrar la primera →
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
