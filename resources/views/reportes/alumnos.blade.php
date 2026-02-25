@extends('layouts.app')

@section('title', 'Reporte — Alumnos Matriculados')
@section('page-title', 'Alumnos Matriculados')
@section('breadcrumb', 'Reportes / Alumnos Matriculados')

@section('page-actions')
    @if ($anioActivo && $alumnos->isNotEmpty())
        <a href="{{ route('reportes.alumnos.pdf') }}?{{ http_build_query(request()->only(['nivel', 'grado', 'seccion'])) }}"
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
    <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-3 flex-wrap">

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

        {{-- Grado (solo si hay nivel seleccionado) --}}
        <select name="grado" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 bg-white focus:outline-none focus:border-primary cursor-pointer
                       {{ $grados->isEmpty() ? 'opacity-40 cursor-not-allowed' : '' }}"
                {{ $grados->isEmpty() ? 'disabled' : '' }}>
            <option value="">Todos los grados</option>
            @foreach ($grados as $grado)
                <option value="{{ $grado->id }}" {{ request('grado') == $grado->id ? 'selected' : '' }}>
                    {{ $grado->nombre }}
                </option>
            @endforeach
        </select>

        {{-- Sección (solo si hay grado seleccionado) --}}
        <select name="seccion" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 bg-white focus:outline-none focus:border-primary cursor-pointer
                       {{ $secciones->isEmpty() ? 'opacity-40 cursor-not-allowed' : '' }}"
                {{ $secciones->isEmpty() ? 'disabled' : '' }}>
            <option value="">Todas las secciones</option>
            @foreach ($secciones as $seccion)
                <option value="{{ $seccion->id }}" {{ request('seccion') == $seccion->id ? 'selected' : '' }}>
                    {{ $seccion->nombre }}
                </option>
            @endforeach
        </select>

        {{-- Limpiar filtros --}}
        @if (request()->anyFilled(['nivel', 'grado', 'seccion']))
            <a href="{{ route('reportes.alumnos') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-500 hover:text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-xmark text-xs"></i>
                Limpiar
            </a>
        @endif

    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">
                {{ $alumnos->count() }} {{ $alumnos->count() === 1 ? 'alumno' : 'alumnos' }}
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
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Apellidos y Nombres</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Grado</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Sección</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Apoderado</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($alumnos as $i => $alumno)
                        @php $ins = $alumno->inscripcionesPago->first() @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $alumno->dni }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-800">{{ $alumno->nombre_completo }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $ins?->nivelEducativo?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $ins?->grado?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $ins?->seccion?->nombre ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $alumno->apoderado?->nombre_completo ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $alumno->apoderado?->telefono ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-users text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay alumnos con los filtros seleccionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
