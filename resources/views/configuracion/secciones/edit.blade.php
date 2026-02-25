@extends('layouts.app')

@section('title', 'Editar Sección')
@section('page-title', 'Editar Sección')
@section('breadcrumb', 'Configuración / Secciones / Editar')

@section('content')

    <div class="max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0 mt-0.5"></i>
                    <ul class="text-sm text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Alpine.js: select cascading nivel → grado --}}
            <script>
                window._gradosData = {{ Js::from($grados) }};
            </script>
            <form method="POST" action="{{ route('configuracion.secciones.update', $seccion) }}"
                  x-data="{
                      allGrados: window._gradosData,
                      selectedNivel: '{{ old('id_educativo', $seccion->id_educativo) }}',
                      selectedGrado: '{{ old('id_grado', $seccion->id_grado) }}',
                      get filteredGrados() {
                          if (!this.selectedNivel) return [];
                          return this.allGrados.filter(g => g.id_educativo == this.selectedNivel);
                      }
                  }">
                @csrf
                @method('PUT')

                <div class="space-y-5">

                    <div>
                        <label for="id_educativo" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Nivel Educativo <span class="text-red-500">*</span>
                        </label>
                        <select id="id_educativo"
                                name="id_educativo"
                                x-model="selectedNivel"
                                @change="selectedGrado = ''"
                                class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_educativo') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                       text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                            <option value="">Seleccionar nivel</option>
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id }}">
                                    {{ $nivel->nombre }}
                                    @if ($nivel->anioAcademico)
                                        ({{ $nivel->anioAcademico->nombre }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_educativo')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_grado" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Grado <span class="text-red-500">*</span>
                        </label>
                        <select id="id_grado"
                                name="id_grado"
                                x-model="selectedGrado"
                                :disabled="!selectedNivel"
                                class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_grado') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                       text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Seleccionar grado</option>
                            <template x-for="grado in filteredGrados" :key="grado.id">
                                <option :value="grado.id" :selected="selectedGrado == grado.id" x-text="grado.nombre"></option>
                            </template>
                        </select>
                        @error('id_grado')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Nombre de la sección <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre', $seccion->nombre) }}"
                               maxlength="10"
                               placeholder="ej: A, B, Única"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        @error('nombre')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar cambios
                    </button>
                    <a href="{{ route('configuracion.secciones.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection
