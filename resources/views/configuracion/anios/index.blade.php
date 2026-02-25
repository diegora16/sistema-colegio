@extends('layouts.app')

@section('title', 'Años Académicos')
@section('page-title', 'Años Académicos')
@section('breadcrumb', 'Configuración / Años Académicos')

@section('content')

    {{-- Info auto-generación --}}
    <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
        <i class="fa-solid fa-circle-info text-blue-400 flex-shrink-0 mt-0.5"></i>
        <p class="text-sm text-blue-700">
            Los años académicos se generan <strong>automáticamente</strong> cada 1 de enero según el reloj GMT-5,
            incluyendo sus niveles y grados. Para consultar datos de un año anterior, actívalo desde esta pantalla
            — el sistema entrará en <strong>modo lectura</strong>.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">
                {{ $anios->count() }} {{ $anios->count() === 1 ? 'año registrado' : 'años registrados' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Año</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Creado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($anios as $anio)
                        @php $esCurrent = (int)$anio->nombre === $anioActual; @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-gray-800 text-base">{{ $anio->nombre }}</span>
                                @if ($esCurrent)
                                    <span class="ml-2 text-[10px] font-semibold text-primary bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">Año en curso</span>
                                @endif
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

                                    {{-- Activar: solo si está cerrado --}}
                                    @if ($anio->estado === 'cerrado')
                                        <form method="POST" action="{{ route('configuracion.anios.activar', $anio) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200">
                                                <i class="fa-solid fa-circle-play text-[10px]"></i>
                                                {{ $esCurrent ? 'Volver al año actual' : 'Activar (modo lectura)' }}
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Eliminar: solo si no tiene datos --}}
                                    @if ($anio->estado === 'cerrado')
                                        <button @click="$dispatch('open-modal', 'modal-anio-{{ $anio->id }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                        </button>
                                        <x-modal-confirm
                                            id="modal-anio-{{ $anio->id }}"
                                            title="Eliminar año académico"
                                            message="¿Estás seguro de que deseas eliminar el año {{ $anio->nombre }}? Esta acción no se puede deshacer."
                                            action="{{ route('configuracion.anios.destroy', $anio) }}"
                                        />
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-calendar-xmark text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay años académicos registrados.</p>
                                    <p class="text-xs text-gray-400">Se crearán automáticamente cada 1 de enero.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
