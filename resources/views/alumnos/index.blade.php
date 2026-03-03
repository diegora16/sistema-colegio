@extends('layouts.app')

@section('title', $verInactivos ? 'Alumnos Inactivos' : 'Alumnos')
@section('page-title', $verInactivos ? 'Alumnos Inactivos' : 'Alumnos')
@section('breadcrumb', $verInactivos ? 'Alumnos / Inactivos' : 'Alumnos')

@section('page-actions')
    <div class="flex items-center gap-2">
        {{-- Toggle activos / inactivos --}}
        @if ($verInactivos)
            <a href="{{ route('alumnos.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-users text-xs"></i> Ver activos
            </a>
        @else
            <a href="{{ route('alumnos.index', ['inactivos' => 1]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="fa-solid fa-user-slash text-xs"></i> Ver inactivos
            </a>
            @if ($anioActivo)
                <a href="{{ route('alumnos.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Nuevo Alumno
                </a>
            @endif
        @endif
    </div>
@endsection

@section('content')

    @if (!$anioActivo && !$verInactivos)
        <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. Activa uno en <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Configuración → Años</a> para gestionar alumnos.</p>
        </div>
    @endif

    @if ($verInactivos)
        <div class="mb-5 flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-info text-gray-400 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-gray-500">
                Alumnos <strong>retirados</strong> (se fueron del colegio) y <strong>egresados</strong> (terminaron 5° Secundaria).
                Usa <strong>Reactivar</strong> si un alumno vuelve al colegio — aparecerá en "Promover Alumnos" para asignarle el grado correcto del año activo.
            </p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
            <p class="text-sm text-gray-500 shrink-0">
                @if ($verInactivos)
                    {{ $alumnos->total() }} {{ $alumnos->total() === 1 ? 'alumno inactivo' : 'alumnos inactivos' }}
                    <span class="text-gray-400">— retirados y egresados</span>
                @else
                    {{ $alumnos->total() }} {{ $alumnos->total() === 1 ? 'alumno registrado' : 'alumnos registrados' }}
                    @if ($anioActivo)
                        <span class="text-gray-400">— Año {{ $anioActivo->nombre }}</span>
                    @endif
                @endif
            </p>
            <form method="GET" action="{{ route('alumnos.index') }}"
                  class="flex items-center w-full sm:w-72 border border-gray-200 bg-gray-50 rounded-xl px-3.5
                         focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-colors">
                @if ($verInactivos)
                    <input type="hidden" name="inactivos" value="1">
                @endif
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs shrink-0"></i>
                <input type="text" name="q" value="{{ $busqueda }}"
                       placeholder="Buscar por nombre o DNI..."
                       class="w-full pl-2.5 py-2 bg-transparent text-sm text-gray-800 focus:outline-none">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Grado / Sección</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Apoderado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($alumnos as $alumno)
                        @php
                            if ($verInactivos) {
                                $nivel     = $alumno->nivelEducativo?->nombre ?? '—';
                                $grado     = $alumno->grado?->nombre ?? '—';
                                $seccion   = $alumno->seccion;
                                $anioNivel = $alumno->nivelEducativo?->anioAcademico?->nombre;
                                $sinMatric = false;
                            } else {
                                $ins       = $alumno->inscripcionesPago->first();
                                $nivel     = $ins?->nivelEducativo?->nombre ?? $alumno->nivelEducativo?->nombre ?? '—';
                                $grado     = $ins?->grado?->nombre       ?? $alumno->grado?->nombre       ?? '—';
                                $seccion   = $ins?->seccion               ?? $alumno->seccion;
                                $anioNivel = null;
                                $sinMatric = !$ins;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors {{ $verInactivos ? 'opacity-80' : '' }}">
                            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $alumno->dni }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800 text-sm">{{ $alumno->nombre_completo }}</div>
                                @if ($verInactivos)
                                    @if ($alumno->estado === 'egresado')
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                            <i class="fa-solid fa-graduation-cap text-[9px]"></i> Egresado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-200 text-gray-500">
                                            <i class="fa-solid fa-user-slash text-[9px]"></i> Retirado
                                        </span>
                                    @endif
                                @elseif ($sinMatric)
                                    <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600">
                                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Sin matricular
                                    </span>
                                @elseif ($alumno->correo)
                                    <div class="text-gray-400 text-xs">{{ $alumno->correo }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $nivel }}
                                @if ($anioNivel)
                                    <span class="block text-gray-400 text-[10px]">Año {{ $anioNivel }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-gray-700 text-xs font-medium">{{ $grado }}</span>
                                @if ($seccion)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $seccion->nombre }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-gray-700 text-xs font-medium">{{ $alumno->apoderado->nombre_completo ?? '—' }}</div>
                                @if ($alumno->apoderado?->telefono)
                                    <div class="text-gray-400 text-xs">{{ $alumno->apoderado->telefono }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    @if ($verInactivos)
                                        {{-- Acciones para inactivos --}}
                                        <a href="{{ route('alumnos.edit', $alumno) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                            <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                        </a>
                                        <form method="POST" action="{{ route('alumnos.reactivar', $alumno) }}"
                                              onsubmit="return confirm('¿Reactivar a {{ addslashes($alumno->nombre_completo) }}? Podrás asignarle el grado correcto del año activo.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                                <i class="fa-solid fa-rotate text-[10px]"></i> Reactivar
                                            </button>
                                        </form>
                                    @else
                                        {{-- Acciones para activos --}}
                                        @if ($sinMatric)
                                            <a href="{{ route('pagos.matricular', ['alumno_id' => $alumno->id]) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                                <i class="fa-solid fa-graduation-cap text-[10px]"></i> Matricular
                                            </a>
                                        @endif
                                        <a href="{{ route('alumnos.edit', $alumno) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                            <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                        </a>
                                        <form method="POST" action="{{ route('alumnos.dar_baja', $alumno) }}"
                                              onsubmit="return confirm('¿Dar de baja a {{ addslashes($alumno->nombre_completo) }}? Podrás reactivarlo desde la lista de inactivos.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                                <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i> Dar de baja
                                            </button>
                                        </form>
                                        <button @click="$dispatch('open-modal', 'modal-alumno-{{ $alumno->id }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                        </button>
                                        <x-modal-confirm
                                            id="modal-alumno-{{ $alumno->id }}"
                                            title="Eliminar alumno"
                                            message="¿Estás seguro de que deseas eliminar a '{{ $alumno->nombre_completo }}'? Esta acción no se puede deshacer."
                                            action="{{ route('alumnos.destroy', $alumno) }}"
                                        />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-{{ $verInactivos ? 'user-slash' : 'user-graduate' }} text-4xl"></i>
                                    <p class="text-sm text-gray-400">
                                        {{ $verInactivos ? 'No hay alumnos inactivos.' : 'No hay alumnos registrados.' }}
                                    </p>
                                    @if (!$verInactivos && $anioActivo)
                                        <a href="{{ route('alumnos.create') }}"
                                           class="mt-1 text-xs text-primary hover:text-primary-dark font-medium">
                                            Registrar el primero →
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($alumnos->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $alumnos->links() }}
            </div>
        @endif

    </div>

@endsection
