@extends('layouts.app')

@section('title', 'Matricular Alumno')
@section('page-title', 'Matricular Alumno')
@section('breadcrumb', 'Pagos / Matricular Alumno')

@section('content')

    @if (!$anioActivo)
        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-700">No hay un año académico activo. No se pueden crear matrículas.</p>
        </div>
    @elseif (!$tipoMatricula)
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-red-700">No se encontró el tipo de pago "Matrícula" en la base de datos. Ejecuta los seeders.</p>
        </div>
    @else

    @php
        $alumnosJson = $alumnos->map(fn($a) => [
            'id'          => $a->id,
            'nombre'      => $a->nombre_completo,
            'dni'         => $a->dni,
            'info'        => implode(' — ', array_filter([
                                $a->nivelEducativo?->nombre,
                                $a->grado?->nombre ? ($a->grado->nombre . ($a->seccion ? ' ' . $a->seccion->nombre : '')) : null,
                             ])),
            'matriculado' => in_array($a->id, $yaMatriculados),
        ]);

        // Si hay un old('id_alumno') válido, pre-llenar el campo de texto
        $oldAlumno = old('id_alumno')
            ? $alumnos->firstWhere('id', old('id_alumno'))
            : null;
        $oldTexto  = $oldAlumno
            ? $oldAlumno->nombre_completo . ' (' . $oldAlumno->dni . ')'
            : '';
    @endphp

    <div class="max-w-xl">

        {{-- Aviso informativo --}}
        <div class="mb-4 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-info text-blue-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-blue-700">
                Al <span class="font-semibold">completar el pago</span> de la matrícula, el sistema generará automáticamente
                las mensualidades desde el mes de matrícula hasta diciembre.
            </p>
        </div>

        <form action="{{ route('pagos.matricular.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">

                {{-- Cabecera --}}
                <div class="px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Nueva Matrícula</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Año académico activo: <span class="font-semibold text-gray-600">{{ $anioActivo->nombre }}</span>
                    </p>
                </div>

                <div class="px-6 py-5 space-y-5">

                    {{-- Búsqueda de alumno --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Alumno <span class="text-red-500">*</span>
                        </label>

                        <div x-data="{
                                q: '{{ $oldTexto }}',
                                alumnoId: '{{ old('id_alumno', '') }}',
                                abierto: false,
                                alumnos: {{ \Illuminate\Support\Js::from($alumnosJson) }},
                                get filtrados() {
                                    const q = this.q.toLowerCase().trim();
                                    if (q.length < 2) return [];
                                    return this.alumnos.filter(a =>
                                        a.nombre.toLowerCase().includes(q) || a.dni.includes(q)
                                    ).slice(0, 12);
                                },
                                seleccionar(a) {
                                    if (a.matriculado) return;
                                    this.alumnoId = a.id;
                                    this.q = a.nombre + ' (' + a.dni + ')';
                                    this.abierto = false;
                                },
                                limpiar() {
                                    this.alumnoId = '';
                                    this.q = '';
                                    this.$refs.input.focus();
                                }
                             }"
                             @click.outside="abierto = false"
                             class="relative">

                            <input type="hidden" name="id_alumno" :value="alumnoId">

                            <div class="relative">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>
                                <input type="text"
                                       x-ref="input"
                                       x-model="q"
                                       @input="abierto = true; alumnoId = ''"
                                       @focus="abierto = q.trim().length >= 2"
                                       placeholder="Buscar por nombre o DNI…"
                                       autocomplete="off"
                                       class="w-full pl-9 pr-9 py-2.5 rounded-xl border text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                              {{ $errors->has('id_alumno') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                                <button type="button" x-show="q" @click="limpiar()" x-cloak
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>

                            <p class="mt-1 text-[11px] text-gray-400">Escribe al menos 2 caracteres del nombre o el DNI.</p>

                            {{-- Dropdown resultados --}}
                            <div x-show="abierto && filtrados.length > 0" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-20 mt-1 w-full bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden">
                                <template x-for="a in filtrados" :key="a.id">
                                    <button type="button"
                                            @click="seleccionar(a)"
                                            :disabled="a.matriculado"
                                            :class="a.matriculado
                                                ? 'opacity-60 cursor-not-allowed bg-gray-50'
                                                : 'hover:bg-primary/5 cursor-pointer'"
                                            class="w-full flex items-center justify-between px-4 py-2.5 text-left border-b border-gray-50 last:border-0 transition-colors">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-sm text-gray-800" x-text="a.nombre"></span>
                                                <span class="text-xs text-gray-400 font-mono flex-shrink-0" x-text="a.dni"></span>
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5" x-text="a.info"></div>
                                        </div>
                                        <span x-show="a.matriculado"
                                              class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full flex-shrink-0 ml-3">
                                            Ya matriculado
                                        </span>
                                    </button>
                                </template>
                            </div>

                            {{-- Sin resultados --}}
                            <div x-show="abierto && q.trim().length >= 2 && filtrados.length === 0" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white rounded-xl border border-gray-200 shadow-lg px-4 py-3">
                                <p class="text-sm text-gray-400">
                                    No se encontraron alumnos con "<span class="font-medium text-gray-600" x-text="q"></span>".
                                </p>
                            </div>

                        </div>

                        @error('id_alumno')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Costo y Fecha --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label for="costo_total" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Costo de matrícula (S/) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="costo_total" name="costo_total"
                                   step="0.01" min="0.01" max="9999.99"
                                   value="{{ old('costo_total') }}"
                                   placeholder="0.00"
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                          {{ $errors->has('costo_total') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                            @error('costo_total')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_registro" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Fecha de matrícula <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="fecha_registro" name="fecha_registro"
                                   value="{{ old('fecha_registro', date('Y-m-d')) }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                          {{ $errors->has('fecha_registro') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                            <p class="mt-1 text-[11px] text-gray-400">El mes de esta fecha determina desde qué mes se generan las mensualidades.</p>
                            @error('fecha_registro')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                </div>

                {{-- Acciones --}}
                <div class="px-6 py-4 flex items-center justify-end gap-3">
                    <a href="{{ route('pagos.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-graduation-cap text-xs mr-1"></i>
                        Crear Matrícula
                    </button>
                </div>

            </div>
        </form>
    </div>

    @endif

@endsection
