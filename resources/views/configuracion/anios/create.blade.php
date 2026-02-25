@extends('layouts.app')

@section('title', 'Nuevo Año Académico')
@section('page-title', 'Nuevo Año Académico')
@section('breadcrumb', 'Configuración / Años Académicos / Nuevo')

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

            <form method="POST" action="{{ route('configuracion.anios.store') }}">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label for="nombre" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                            Año <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre') }}"
                               maxlength="4"
                               placeholder="ej: 2026"
                               class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                                      text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 transition-colors">
                        @error('nombre')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-400 text-xs mt-1.5">El año se creará en estado <strong>cerrado</strong>. Actívalo desde la lista.</p>
                    </div>

                </div>

                <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Guardar
                    </button>
                    <a href="{{ route('configuracion.anios.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection
