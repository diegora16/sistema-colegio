@extends('layouts.app')

@section('title', 'Editar Apoderado')
@section('page-title', 'Editar Apoderado')
@section('breadcrumb', 'Alumnos / Apoderados / Editar')

@section('content')

    <div class="max-w-lg">
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

            <form method="POST" action="{{ route('apoderados.update', $apoderado) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2 sm:w-48">
                        <label for="dni" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="dni" name="dni"
                               value="{{ old('dni', $apoderado->dni) }}" maxlength="8"
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
                               value="{{ old('nombres', $apoderado->nombres) }}" maxlength="100"
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
                               value="{{ old('apellido_p', $apoderado->apellido_p) }}" maxlength="50"
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
                               value="{{ old('apellido_m', $apoderado->apellido_m) }}" maxlength="50"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('apellido_m') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        @error('apellido_m')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Teléfono
                        </label>
                        <input type="text" id="telefono" name="telefono"
                               value="{{ old('telefono', $apoderado->telefono) }}" maxlength="15"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                    </div>

                    <div>
                        <label for="correo" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Correo
                        </label>
                        <input type="email" id="correo" name="correo"
                               value="{{ old('correo', $apoderado->correo) }}" maxlength="100"
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
                    <a href="{{ route('apoderados.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection
