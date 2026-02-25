@extends('layouts.app')

@section('title', 'Grados')
@section('page-title', 'Grados')
@section('breadcrumb', 'Configuración / Grados')

@section('page-actions')
    <a href="{{ route('configuracion.grados.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
        <i class="fa-solid fa-plus text-xs"></i>
        Nuevo Grado
    </a>
@endsection

@section('content')

    @if (!$anioActivo)
        <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. Activa uno en <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Años Académicos</a> para ver los grados correspondientes.</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Encabezado tabla --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                {{ $grados->count() }} {{ $grados->count() === 1 ? 'grado registrado' : 'grados registrados' }}
                @if ($anioActivo)
                    <span class="text-gray-400">— Año {{ $anioActivo->nombre }}</span>
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Año</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Creado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($grados as $grado)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $grado->id }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-800">{{ $grado->nombre }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $grado->nivelEducativo->nombre ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $grado->nivelEducativo->anioAcademico->nombre ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs">
                                {{ $grado->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('configuracion.grados.edit', $grado) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                    </a>
                                    <button @click="$dispatch('open-modal', 'modal-grado-{{ $grado->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                    </button>
                                </div>
                                <x-modal-confirm
                                    id="modal-grado-{{ $grado->id }}"
                                    title="Eliminar grado"
                                    message="¿Estás seguro de que deseas eliminar el grado '{{ $grado->nombre }}'? Esta acción no se puede deshacer."
                                    action="{{ route('configuracion.grados.destroy', $grado) }}"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-graduation-cap text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay grados registrados.</p>
                                    <a href="{{ route('configuracion.grados.create') }}"
                                       class="mt-1 text-xs text-primary hover:text-primary-dark font-medium">
                                        Crear el primero →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
