@extends('layouts.app')

@section('title', 'Apoderados')
@section('page-title', 'Apoderados')
@section('breadcrumb', 'Alumnos / Apoderados')

@section('content')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
            <p class="text-sm text-gray-500 shrink-0">
                {{ $apoderados->total() }} {{ $apoderados->total() === 1 ? 'apoderado registrado' : 'apoderados registrados' }}
            </p>
            <form method="GET" action="{{ route('apoderados.index') }}"
                  class="flex items-center w-full sm:w-64 border border-gray-200 bg-gray-50 rounded-xl px-3.5
                         focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs shrink-0"></i>
                <input type="text" name="q" value="{{ $busqueda }}"
                       placeholder="Buscar por nombre o DNI..."
                       class="w-full pl-2.5 py-2 bg-transparent text-sm text-gray-800 focus:outline-none">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">DNI</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Correo</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alumnos</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($apoderados as $apoderado)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $apoderado->dni }}</td>
                            <td class="px-5 py-3.5 font-semibold text-gray-800">
                                {{ $apoderado->nombres }} {{ $apoderado->apellido_p }} {{ $apoderado->apellido_m }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $apoderado->telefono ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $apoderado->correo ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $apoderado->alumnos_count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $apoderado->alumnos_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('apoderados.edit', $apoderado) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-[10px]"></i> Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <i class="fa-solid fa-users text-4xl"></i>
                                    <p class="text-sm text-gray-400">No hay apoderados registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($apoderados->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $apoderados->links() }}
            </div>
        @endif

    </div>

@endsection
