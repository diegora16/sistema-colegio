@extends('layouts.app')

@section('title', 'Promover Alumnos')
@section('page-title', 'Promover Alumnos')
@section('breadcrumb', 'Alumnos / Promover Alumnos')

@section('content')

    {{-- Explicación --}}
    <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
        <i class="fa-solid fa-circle-info text-blue-400 flex-shrink-0 mt-0.5"></i>
        <div class="text-sm text-blue-700 space-y-1">
            <p>
                <span class="font-semibold">Alumnos regulares:</span>
                Marcados por defecto para avanzar al siguiente grado en <strong>{{ $anioActivo->nombre }}</strong>.
                Desmarca a los que <strong>repetirán</strong> el mismo grado.
            </p>
            <p>
                <span class="font-semibold">Egresados (5° Secundaria):</span>
                Por defecto se confirma su egreso. Marca a los que <strong>repetirán 5°</strong>.
                Los no marcados quedarán registrados como egresados y no aparecerán en futuras listas.
            </p>
        </div>
    </div>

    @if ($porPromover->isEmpty() && $egresados->isEmpty())

        {{-- Sin alumnos pendientes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fa-solid fa-circle-check text-4xl text-green-400 mb-3"></i>
            <p class="text-gray-600 font-medium">Todos los alumnos ya están en el año {{ $anioActivo->nombre }}.</p>
            <p class="text-sm text-gray-400 mt-1">No hay ningún alumno pendiente de promoción.</p>
            <a href="{{ route('alumnos.index') }}"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver a la lista
            </a>
        </div>

    @else

        <form method="POST" action="{{ route('alumnos.promover.ejecutar') }}">
            @csrf

            {{-- ── Alumnos para promover / repetir ─────────────────────────────── --}}
            @if ($porPromover->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">

                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-gray-800">Alumnos para promover</h2>
                            <p class="text-sm text-gray-400 mt-0.5">{{ $porPromover->count() }} alumno(s) — desmarca los que repetirán el mismo grado</p>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none" x-data>
                            <input type="checkbox" checked
                                   class="w-4 h-4 accent-primary"
                                   @change="$dispatch('toggle-all-promover', { checked: $event.target.checked })">
                            Seleccionar todos
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 w-10"></th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel actual</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Grado actual</th>
                                    <th class="text-center px-3 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider"></th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nivel nuevo</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Grado nuevo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($porPromover as $alumno)
                                    <tr x-data="{ pro: true }"
                                        @toggle-all-promover.window="pro = $event.detail.checked"
                                        :class="pro ? 'hover:bg-gray-50/60' : 'bg-amber-50/30 hover:bg-amber-50/50'"
                                        class="transition-colors">
                                        <td class="px-4 py-3.5 text-center">
                                            <input type="checkbox"
                                                   name="promover_ids[]"
                                                   value="{{ $alumno->id }}"
                                                   class="w-4 h-4 accent-primary"
                                                   x-model="pro" checked>
                                        </td>
                                        <td class="px-5 py-3.5 font-medium" :class="pro ? 'text-gray-800' : 'text-gray-500'">
                                            {{ $alumno->nombre_completo }}
                                        </td>
                                        <td class="px-5 py-3.5 font-mono text-gray-500 text-xs">{{ $alumno->dni }}</td>
                                        <td class="px-5 py-3.5 text-gray-500">{{ $alumno->nivelEducativo?->nombre ?? '—' }}</td>
                                        <td class="px-5 py-3.5 text-gray-500">{{ $alumno->grado?->nombre ?? '—' }}</td>
                                        <td class="px-3 py-3.5 text-center text-gray-300">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <template x-if="pro">
                                                <span class="font-semibold text-primary">{{ $alumno->nuevoNivel?->nombre ?? '—' }}</span>
                                            </template>
                                            <template x-if="!pro">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                    <i class="fa-solid fa-rotate-left text-[9px]"></i> Repetirá
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <template x-if="pro">
                                                <span class="font-semibold text-primary">{{ $alumno->nuevoGrado?->nombre ?? '—' }}</span>
                                            </template>
                                            <template x-if="!pro">
                                                <span class="text-gray-400 text-xs">{{ $alumno->grado?->nombre ?? '—' }}</span>
                                            </template>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── Egresados (5° Secundaria) ────────────────────────────────────── --}}
            @if ($egresados->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden mb-5">
                    <div class="px-5 py-4 border-b border-amber-100 flex items-center gap-3">
                        <i class="fa-solid fa-graduation-cap text-amber-500"></i>
                        <div>
                            <h2 class="font-semibold text-gray-800">Alumnos que terminaron 5° Secundaria</h2>
                            <p class="text-sm text-gray-400 mt-0.5">
                                Por defecto se confirma su egreso. Marca solo los que <strong class="text-amber-700">repetirán 5°</strong>.
                            </p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 border-b border-amber-100">
                                <tr>
                                    <th class="px-4 py-3 w-10 text-[11px] font-semibold text-gray-500 uppercase tracking-wider text-center">Repetirá</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Estado tras confirmar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                @foreach ($egresados as $alumno)
                                    <tr x-data="{ rep: false }"
                                        :class="rep ? 'bg-amber-50/60' : ''"
                                        class="hover:bg-amber-50/40 transition-colors">
                                        <td class="px-4 py-3.5 text-center">
                                            <input type="checkbox"
                                                   name="repetir_egresado_ids[]"
                                                   value="{{ $alumno->id }}"
                                                   class="w-4 h-4 accent-primary"
                                                   x-model="rep">
                                        </td>
                                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $alumno->nombre_completo }}</td>
                                        <td class="px-5 py-3.5 font-mono text-gray-500 text-xs">{{ $alumno->dni }}</td>
                                        <td class="px-5 py-3.5">
                                            <template x-if="!rep">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                    <i class="fa-solid fa-graduation-cap text-[9px]"></i> Egresa — se marcará como egresado
                                                </span>
                                            </template>
                                            <template x-if="rep">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                                    <i class="fa-solid fa-rotate-left text-[9px]"></i> Repetirá 5° Secundaria en {{ $anioActivo->nombre }}
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── Acciones ─────────────────────────────────────────────────────── --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-check text-xs"></i>
                    Confirmar y aplicar cambios
                </button>
                <a href="{{ route('alumnos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Cancelar
                </a>
            </div>

        </form>

    @endif

@endsection
