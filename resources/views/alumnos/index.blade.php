@extends('layouts.app')

@section('title', 'Alumnos')
@section('page-title', 'Alumnos')
@section('breadcrumb', 'Alumnos')

@section('page-actions')
    @if ($anioActivo)
        <a href="{{ route('alumnos.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            Nuevo Alumno
        </a>
    @endif
@endsection

@section('content')

    @if (!$anioActivo)
        <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. Activa uno en <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Configuración → Años</a> para gestionar alumnos.</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ search: '' }">

        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
            <p class="text-sm text-gray-500 shrink-0">
                {{ $alumnos->count() }} {{ $alumnos->count() === 1 ? 'alumno registrado' : 'alumnos registrados' }}
                @if ($anioActivo)
                    <span class="text-gray-400">— Año {{ $anioActivo->nombre }}</span>
                @endif
            </p>
            <div class="flex items-center w-full sm:w-72 border border-gray-200 bg-gray-50 rounded-xl px-3.5
                        focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs shrink-0"></i>
                <input type="text"
                       x-model="search"
                       placeholder="Buscar por nombre, DNI, grado..."
                       class="w-full pl-2.5 py-2 bg-transparent text-sm text-gray-800 focus:outline-none">
            </div>
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
                        <tr class="hover:bg-gray-50/60 transition-colors"
                            x-show="!search || $el.dataset.search.includes(search.toLowerCase())"
                            data-search="{{ strtolower($alumno->nombre_completo . ' ' . $alumno->dni . ' ' . ($alumno->nivelEducativo?->nombre ?? '') . ' ' . ($alumno->grado?->nombre ?? '') . ' ' . ($alumno->seccion?->nombre ?? '') . ' ' . ($alumno->apoderado?->nombre_completo ?? '')) }}">
                            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $alumno->dni }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800 text-sm">{{ $alumno->nombre_completo }}</div>
                                @if ($alumno->correo)
                                    <div class="text-gray-400 text-xs">{{ $alumno->correo }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $alumno->nivelEducativo->nombre ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-gray-700 text-xs font-medium">{{ $alumno->grado->nombre ?? '—' }}</span>
                                @if ($alumno->seccion)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $alumno->seccion->nombre }}
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
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('alumnos.edit', $alumno) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                    </a>
                                    <button @click="$dispatch('open-modal', 'modal-alumno-{{ $alumno->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                    </button>
                                </div>
                                <x-modal-confirm
                                    id="modal-alumno-{{ $alumno->id }}"
                                    title="Eliminar alumno"
                                    message="¿Estás seguro de que deseas eliminar a '{{ $alumno->nombre_completo }}'? Esta acción no se puede deshacer."
                                    action="{{ route('alumnos.destroy', $alumno) }}"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-user-graduate text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay alumnos registrados.</p>
                                    @if ($anioActivo)
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

    </div>

@endsection
