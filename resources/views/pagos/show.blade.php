@extends('layouts.app')

@section('title', 'Detalle de Inscripción')
@section('page-title', 'Detalle de Inscripción')
@section('breadcrumb', 'Pagos / Detalle')

@section('content')

@php
    $totalPagado = $inscripcion->pagos->sum('aporte');
    $saldo       = (float) $inscripcion->costo_total - (float) $totalPagado;
    $porcentaje  = $inscripcion->costo_total > 0
        ? min(100, round(($totalPagado / $inscripcion->costo_total) * 100, 1))
        : 0;
    $badges = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'parcial'   => 'bg-blue-100 text-blue-700',
        'pagado'    => 'bg-green-100 text-green-700',
    ];
    $labels = [
        'pendiente' => 'Pendiente',
        'parcial'   => 'Parcial',
        'pagado'    => 'Pagado',
    ];
    $esMatricula   = strtolower($inscripcion->tipoPago->nombre) === 'matrícula';
    $confirmEliminar = $esMatricula
        ? '¿Eliminar esta inscripción? Se eliminarán los comprobantes y las mensualidades pendientes asociadas.'
        : '¿Eliminar esta inscripción? Se eliminarán los comprobantes registrados.';
@endphp

    {{-- Volver + Eliminar inscripción --}}
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('pagos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Volver a Pagos
        </a>

        <form method="POST" action="{{ route('pagos.destroy', $inscripcion) }}" x-data>
            @csrf
            @method('DELETE')
            <button type="submit"
                    @click.prevent="if(confirm({{ Js::from($confirmEliminar) }})) $el.closest('form').submit()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                <i class="fa-solid fa-trash text-[10px]"></i>
                Eliminar inscripción
            </button>
        </form>
    </div>

    {{-- Info: Alumno + Estado --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

        {{-- Card Alumno --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Alumno</h3>
            <p class="text-base font-bold text-gray-800 mb-1">{{ $inscripcion->alumno->nombre_completo }}</p>
            <p class="text-xs text-gray-500 font-mono mb-3">DNI: {{ $inscripcion->alumno->dni }}</p>
            <div class="space-y-1.5 text-xs text-gray-600">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-gray-400 w-4 text-center"></i>
                    <span>{{ $inscripcion->alumno->nivelEducativo?->nombre ?? '—' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-gray-400 w-4 text-center"></i>
                    <span>{{ $inscripcion->alumno->grado?->nombre ?? '—' }}
                        @if ($inscripcion->alumno->seccion)
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700">
                                {{ $inscripcion->alumno->seccion->nombre }}
                            </span>
                        @endif
                    </span>
                </div>
                @if ($inscripcion->alumno->apoderado)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user text-gray-400 w-4 text-center"></i>
                        <span>{{ $inscripcion->alumno->apoderado->nombre_completo }}</span>
                    </div>
                    @if ($inscripcion->alumno->apoderado->telefono)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-gray-400 w-4 text-center"></i>
                            <span>{{ $inscripcion->alumno->apoderado->telefono }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Card Estado de Pago --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado de pago</h3>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $badges[$inscripcion->estado] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ $labels[$inscripcion->estado] ?? $inscripcion->estado }}
                </span>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Tipo</span>
                    <span class="font-semibold text-gray-700">
                        {{ $inscripcion->tipoPago->nombre }}
                        @if ($inscripcion->mes)
                            — {{ $inscripcion->mes->nombre }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Año académico</span>
                    <span class="font-semibold text-gray-700">{{ $inscripcion->anioAcademico?->nombre ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Fecha registro</span>
                    <span class="font-semibold text-gray-700">{{ $inscripcion->fecha_registro->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Barra de progreso --}}
            <div class="mb-3">
                <div class="flex justify-between text-[11px] text-gray-400 mb-1">
                    <span>Progreso</span>
                    <span>{{ $porcentaje }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all
                        {{ $porcentaje >= 100 ? 'bg-green-500' : ($porcentaje > 0 ? 'bg-primary' : 'bg-gray-200') }}"
                         style="width: {{ $porcentaje }}%"></div>
                </div>
            </div>

            {{-- Totales --}}
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-gray-50 rounded-lg p-2.5">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Total</p>
                    <p class="text-sm font-bold text-gray-700">S/ {{ number_format($inscripcion->costo_total, 2) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-2.5">
                    <p class="text-[10px] text-green-600 uppercase tracking-wide mb-0.5">Pagado</p>
                    <p class="text-sm font-bold text-green-700">S/ {{ number_format($totalPagado, 2) }}</p>
                </div>
                <div class="{{ $saldo > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg p-2.5">
                    <p class="text-[10px] {{ $saldo > 0 ? 'text-red-500' : 'text-gray-400' }} uppercase tracking-wide mb-0.5">Saldo</p>
                    <p class="text-sm font-bold {{ $saldo > 0 ? 'text-red-600' : 'text-gray-400' }}">S/ {{ number_format($saldo, 2) }}</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Historial de pagos --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">

        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Comprobantes registrados</h3>
        </div>

        @if ($inscripcion->pagos->isEmpty())
            <div class="px-5 py-10 text-center">
                <div class="flex flex-col items-center gap-2 text-gray-300">
                    <i class="fa-solid fa-image text-3xl"></i>
                    <p class="text-sm text-gray-400">No hay pagos registrados aún.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="text-center px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Comprobante</th>
                            <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($inscripcion->pagos as $i => $pago)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $pago->fecha->format('d/m/Y') }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs font-semibold text-green-700">
                                    S/ {{ number_format($pago->aporte, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ asset('storage/' . $pago->foto) }}" target="_blank"
                                       class="inline-block group">
                                        <img src="{{ asset('storage/' . $pago->foto) }}"
                                             alt="Comprobante"
                                             class="h-10 w-10 object-cover rounded-lg border border-gray-200 group-hover:border-primary transition-colors">
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST"
                                          action="{{ route('pagos.eliminar_pago', [$inscripcion, $pago]) }}"
                                          x-data>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                @click.prevent="if(confirm('¿Eliminar este comprobante de pago?')) $el.closest('form').submit()"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

    {{-- Aviso mensualidades (solo matrícula pendiente) --}}
    @if ($saldo > 0 && $esMatricula)
        @php
            $mesActualNombre = now()->locale('es')->isoFormat('MMMM');
            $esMesEscolar    = \App\Models\Mes::where('numero', now()->month)->exists();
        @endphp
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-info text-blue-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-blue-700">
                @if ($esMesEscolar)
                    Al completar el pago, se generará automáticamente la mensualidad de
                    <span class="font-semibold">{{ $mesActualNombre }}</span>.
                    Los meses siguientes se generarán el 1ro de cada mes de forma automática.
                @else
                    Al completar el pago, las mensualidades se generarán automáticamente el 1ro de cada mes
                    a partir del inicio del año escolar.
                @endif
            </p>
        </div>
    @endif

    {{-- Formulario Registrar Pago --}}
    @if ($saldo > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Registrar nuevo pago</h3>
                <p class="text-xs text-gray-400 mt-0.5">Saldo pendiente: <span class="font-semibold text-red-600">S/ {{ number_format($saldo, 2) }}</span></p>
            </div>

            <form action="{{ route('pagos.pagar', $inscripcion) }}" method="POST"
                  enctype="multipart/form-data"
                  class="px-6 py-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

                    {{-- Monto --}}
                    <div>
                        <label for="aporte" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Monto (S/) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="aporte" name="aporte"
                               step="0.01" min="0.01" max="{{ $saldo }}"
                               value="{{ old('aporte') }}"
                               placeholder="0.00"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                      {{ $errors->has('aporte') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                        @error('aporte')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label for="fecha" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Fecha del pago <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha" name="fecha"
                               value="{{ old('fecha', date('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                      {{ $errors->has('fecha') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                        @error('fecha')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Foto --}}
                    <div>
                        <label for="foto" class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Comprobante (foto) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" id="foto" name="foto"
                               accept="image/jpeg,image/png,image/webp"
                               class="w-full px-3 py-2 rounded-xl border text-xs text-gray-700 bg-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors
                                      {{ $errors->has('foto') ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-primary' }}">
                        <p class="mt-1 text-[11px] text-gray-400">JPG, PNG o WEBP · Máx. 2 MB</p>
                        @error('foto')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white bg-primary hover:bg-primary-dark rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-file-arrow-up text-xs"></i>
                        Registrar Pago
                    </button>
                </div>

            </form>

        </div>
    @else
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-4">
            <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
            <div>
                <p class="text-sm font-semibold text-green-700">Inscripción pagada completamente</p>
                <p class="text-xs text-green-600">Se han registrado todos los pagos correspondientes.</p>
            </div>
        </div>
    @endif

@endsection
