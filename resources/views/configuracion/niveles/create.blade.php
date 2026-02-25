@extends('layouts.app')

@section('title', 'Nuevo Nivel Educativo')
@section('page-title', 'Nuevo Nivel Educativo')
@section('breadcrumb', 'Configuración / Niveles Educativos / Nuevo')

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

            <form method="POST" action="{{ route('configuracion.niveles.store') }}">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label for="id_año" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Año Académico <span class="text-red-500">*</span>
                        </label>
                        <select id="id_año"
                                name="id_año"
                                class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('id_año') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                       text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                            <option value="">Seleccionar año</option>
                            @foreach ($anios as $anio)
                                <option value="{{ $anio->id }}"
                                    {{ old('id_año', $anioActivo?->id) == $anio->id ? 'selected' : '' }}>
                                    {{ $anio->nombre }}
                                    @if ($anio->estado === 'activo') (activo) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_año')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre') }}"
                               maxlength="50"
                               placeholder="ej: Inicial, Primaria, Secundaria"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        @error('nombre')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="precio" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Precio mensual (S/) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="precio"
                               name="precio"
                               value="{{ old('precio') }}"
                               min="0"
                               max="9999.99"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('precio') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        @error('precio')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-400 text-xs mt-1.5">Monto de la mensualidad para este nivel.</p>
                    </div>

                </div>

                <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar
                    </button>
                    <a href="{{ route('configuracion.niveles.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection
