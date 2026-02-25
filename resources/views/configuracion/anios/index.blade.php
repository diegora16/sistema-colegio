@extends('layouts.app')

@section('title', 'Años Académicos')
@section('page-title', 'Años Académicos')
@section('breadcrumb', 'Configuración / Años Académicos')

@section('page-actions')
    <a href="{{ route('configuracion.anios.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
        <i class="fa-solid fa-plus text-xs"></i>
        Nuevo Año
    </a>
@endsection

@section('content')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Encabezado tabla --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                {{ $anios->count() }} {{ $anios->count() === 1 ? 'año registrado' : 'años registrados' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Año</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Creado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($anios as $anio)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $anio->id }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-800">{{ $anio->nombre }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($anio->estado === 'activo')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <i class="fa-solid fa-circle text-[8px]"></i> Cerrado
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs">
                                {{ $anio->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('configuracion.anios.edit', $anio) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                    </a>
                                    <button @click="$dispatch('open-modal', 'modal-anio-{{ $anio->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                    </button>
                                </div>
                                <x-modal-confirm
                                    id="modal-anio-{{ $anio->id }}"
                                    title="Eliminar año académico"
                                    message="¿Estás seguro de que deseas eliminar el año {{ $anio->nombre }}? Esta acción no se puede deshacer."
                                    action="{{ route('configuracion.anios.destroy', $anio) }}"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-calendar-xmark text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay años académicos registrados.</p>
                                    <a href="{{ route('configuracion.anios.create') }}"
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
