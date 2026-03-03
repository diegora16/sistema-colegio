@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Reportes')
@section('breadcrumb', 'Reportes')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

    {{-- Tarjeta 1: Alumnos Matriculados --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-users text-primary text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-base leading-tight">Alumnos Matriculados</h3>
                <p class="text-xs text-gray-400 mt-0.5">Lista de alumnos del año activo</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 leading-relaxed">
            Muestra todos los alumnos registrados en el año académico activo.
            Filtra por nivel educativo, grado y sección.
        </p>
        <a href="{{ route('reportes.alumnos') }}"
           class="mt-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-right text-xs"></i>
            Ver Reporte
        </a>
    </div>

    {{-- Tarjeta 2: Morosidad --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-base leading-tight">Morosidad</h3>
                <p class="text-xs text-gray-400 mt-0.5">Mensualidades pendientes o parciales</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 leading-relaxed">
            Identifica alumnos con mensualidades sin pagar o con pagos incompletos.
            Filtra por nivel educativo y mes.
        </p>
        <a href="{{ route('reportes.morosidad') }}"
           class="mt-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-right text-xs"></i>
            Ver Reporte
        </a>
    </div>

    {{-- Tarjeta 3: Pagos por Alumno --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-file-invoice-dollar text-indigo-500 text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-base leading-tight">Pagos por Alumno</h3>
                <p class="text-xs text-gray-400 mt-0.5">Historial individual de pagos</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 leading-relaxed">
            Consulta el historial completo de pagos de un alumno específico:
            matrícula y todas sus mensualidades.
        </p>
        <a href="{{ route('reportes.pagos_alumno') }}"
           class="mt-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-right text-xs"></i>
            Ver Reporte
        </a>
    </div>

    {{-- Tarjeta 4: Ingresos por Año --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-sack-dollar text-emerald-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-base leading-tight">Ingresos por Año</h3>
                <p class="text-xs text-gray-400 mt-0.5">Total cobrado mes a mes</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 leading-relaxed">
            Resume todos los cobros recibidos por mes (Enero–Diciembre) de cualquier
            año académico, incluyendo el total general.
        </p>
        <a href="{{ route('reportes.ingresos') }}"
           class="mt-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-right text-xs"></i>
            Ver Reporte
        </a>
    </div>

</div>

@endsection
