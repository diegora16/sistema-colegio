@extends('layouts.app')

@section('title', 'Editar Alumno')
@section('page-title', 'Editar Alumno')
@section('breadcrumb', 'Alumnos / Editar')

@section('content')

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

    <script>
        window._gradosData    = {{ Js::from($grados) }};
        window._seccionesData = {{ Js::from($secciones) }};
    </script>

    <form method="POST" action="{{ route('alumnos.update', $alumno) }}"
          x-data="{
              allGrados:     window._gradosData,
              allSecciones:  window._seccionesData,
              selectedNivel:  '{{ old('id_educativo', $alumno->id_educativo) }}',
              selectedGrado:  '{{ old('id_grado', $alumno->id_grado) }}',
              selectedSeccion:'{{ old('id_seccion', $alumno->id_seccion) }}',
              get filteredGrados() {
                  if (!this.selectedNivel) return [];
                  return this.allGrados.filter(g => g.id_educativo == this.selectedNivel);
              },
              get filteredSecciones() {
                  if (!this.selectedGrado) return [];
                  return this.allSecciones.filter(s => s.id_grado == this.selectedGrado);
              }
          }">
        @csrf
        @method('PUT')

        {{-- ── Datos del Apoderado ───────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-5">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-tie text-primary"></i> Datos del Apoderado
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2 sm:w-48">
                    <label for="apoderado_dni" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        DNI <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apoderado_dni" name="apoderado_dni"
                           value="{{ old('apoderado_dni', $alumno->apoderado->dni) }}" maxlength="8"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apoderado_dni') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors font-mono">
                    @error('apoderado_dni')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apoderado_nombres" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apoderado_nombres" name="apoderado_nombres"
                           value="{{ old('apoderado_nombres', $alumno->apoderado->nombres) }}" maxlength="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apoderado_nombres') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apoderado_nombres')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apoderado_apellido_p" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Apellido Paterno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apoderado_apellido_p" name="apoderado_apellido_p"
                           value="{{ old('apoderado_apellido_p', $alumno->apoderado->apellido_p) }}" maxlength="50"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apoderado_apellido_p') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apoderado_apellido_p')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apoderado_apellido_m" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Apellido Materno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apoderado_apellido_m" name="apoderado_apellido_m"
                           value="{{ old('apoderado_apellido_m', $alumno->apoderado->apellido_m) }}" maxlength="50"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apoderado_apellido_m') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apoderado_apellido_m')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apoderado_telefono" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Teléfono
                    </label>
                    <input type="text" id="apoderado_telefono" name="apoderado_telefono"
                           value="{{ old('apoderado_telefono', $alumno->apoderado->telefono) }}" maxlength="15"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>

                <div>
                    <label for="apoderado_correo" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Correo
                    </label>
                    <input type="email" id="apoderado_correo" name="apoderado_correo"
                           value="{{ old('apoderado_correo', $alumno->apoderado->correo) }}" maxlength="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apoderado_correo') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apoderado_correo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ── Datos del Alumno ─────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-graduate text-primary"></i> Datos del Alumno
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label for="id_educativo" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Nivel Educativo <span class="text-red-500">*</span>
                    </label>
                    <select id="id_educativo" name="id_educativo"
                            x-model="selectedNivel"
                            @change="selectedGrado = ''; selectedSeccion = ''"
                            class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_educativo') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                   text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Seleccionar nivel</option>
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_educativo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_grado" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Grado <span class="text-red-500">*</span>
                    </label>
                    <select id="id_grado" name="id_grado"
                            x-model="selectedGrado"
                            @change="selectedSeccion = ''"
                            :disabled="!selectedNivel"
                            class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_grado') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                   text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="">Seleccionar grado</option>
                        <template x-for="g in filteredGrados" :key="g.id">
                            <option :value="g.id" :selected="selectedGrado == g.id" x-text="g.nombre"></option>
                        </template>
                    </select>
                    @error('id_grado')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_seccion" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Sección <span class="text-red-500">*</span>
                    </label>
                    <select id="id_seccion" name="id_seccion"
                            x-model="selectedSeccion"
                            :disabled="!selectedGrado"
                            class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_seccion') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                   text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="">Seleccionar sección</option>
                        <template x-for="s in filteredSecciones" :key="s.id">
                            <option :value="s.id" :selected="selectedSeccion == s.id" x-text="s.nombre"></option>
                        </template>
                    </select>
                    @error('id_seccion')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dni" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        DNI del Alumno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="dni" name="dni"
                           value="{{ old('dni', $alumno->dni) }}" maxlength="8"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('dni') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors font-mono">
                    @error('dni')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="nombres" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombres" name="nombres"
                           value="{{ old('nombres', $alumno->nombres) }}" maxlength="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('nombres') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('nombres')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apellido_p" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Apellido Paterno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apellido_p" name="apellido_p"
                           value="{{ old('apellido_p', $alumno->apellido_p) }}" maxlength="50"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apellido_p') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apellido_p')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apellido_m" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Apellido Materno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apellido_m" name="apellido_m"
                           value="{{ old('apellido_m', $alumno->apellido_m) }}" maxlength="50"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apellido_m') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('apellido_m')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_nacimiento" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Fecha de Nacimiento <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                           value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('fecha_nacimiento') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('fecha_nacimiento')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefono" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Teléfono
                    </label>
                    <input type="text" id="telefono" name="telefono"
                           value="{{ old('telefono', $alumno->telefono) }}" maxlength="15"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                </div>

                <div class="sm:col-span-2">
                    <label for="correo" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Correo
                    </label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo', $alumno->correo) }}" maxlength="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('correo') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                  text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('correo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar cambios
                </button>
                <a href="{{ route('alumnos.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                    Cancelar
                </a>
            </div>

        </div>

    </form>

@endsection
