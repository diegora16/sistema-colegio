<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema') — I.E.P. Jesús y María</title>
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.6.0-web/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased" x-data>

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-64 bg-primary flex flex-col flex-shrink-0 overflow-hidden">

        {{-- Logo + nombre del colegio --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-5 py-4 border-b border-primary-dark flex-shrink-0 hover:bg-primary-dark transition-colors">
            {{-- Contenedor fijo del logo --}}
            <span class="flex-shrink-0 flex items-center justify-center" style="width:44px;height:44px;">
                <img src="{{ asset('img/logo_colegio/logocolegio.png') }}"
                     alt="Logo"
                     style="width:44px;height:44px;object-fit:contain;">
            </span>
            <span class="flex flex-col min-w-0">
                <span class="text-white/50 text-[10px] font-semibold uppercase tracking-widest leading-none">I.E.P.</span>
                <span class="text-white font-bold text-[13px] leading-snug truncate">Jesús y María</span>
            </span>
        </a>

        {{-- Navegación --}}
        @php
            $anioActivo   = \App\Models\AnioAcademico::where('estado', 'activo')->first();
            $anioActual   = (int) now()->setTimezone('America/Lima')->format('Y');
            $modoLectura  = $anioActivo && (int) $anioActivo->nombre !== $anioActual;
        @endphp

        {{-- Indicador de modo lectura --}}
        @if ($modoLectura)
            <div class="mx-3 mt-2 mb-1 px-3 py-2 rounded-lg bg-amber-500/20 border border-amber-400/30">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-eye text-amber-300 text-[10px] flex-shrink-0"></i>
                    <span class="text-amber-200 text-[11px] font-semibold leading-tight">
                        Modo lectura<br>
                        <span class="text-amber-300/80 font-normal">Año {{ $anioActivo->nombre }}</span>
                    </span>
                </div>
            </div>
        @endif

        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">

            {{-- Dashboard (solo año actual) --}}
            @if (!$modoLectura)
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-colors duration-150
                      {{ request()->routeIs('dashboard') ? 'bg-primary-darker text-white' : 'text-white/70 hover:bg-primary-dark hover:text-white' }}">
                <i class="fa-solid fa-gauge-high w-4 text-center text-[11px]"></i>
                Dashboard
            </a>
            @endif

            {{-- Configuración (colapsable) --}}
            <div x-data="{ open: {{ request()->routeIs('configuracion.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-[13px] font-medium transition-colors duration-150
                               {{ request()->routeIs('configuracion.*') ? 'bg-primary-darker text-white' : 'text-white/70 hover:bg-primary-dark hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-sliders w-4 text-center text-[11px]"></i>
                        Configuración
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200 flex-shrink-0"
                       :class="open ? 'rotate-90' : ''"></i>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 ml-3 pl-3 border-l border-primary-dark space-y-0.5">
                    <a href="{{ route('configuracion.anios.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('configuracion.anios.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-calendar-days w-3.5 text-center text-[10px]"></i>
                        Año Académico
                    </a>
                    <a href="{{ route('configuracion.niveles.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('configuracion.niveles.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-layer-group w-3.5 text-center text-[10px]"></i>
                        Nivel Educativo
                    </a>
                    <a href="{{ route('configuracion.grados.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('configuracion.grados.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-list-ol w-3.5 text-center text-[10px]"></i>
                        Grados
                    </a>
                    <a href="{{ route('configuracion.secciones.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('configuracion.secciones.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-door-open w-3.5 text-center text-[10px]"></i>
                        Secciones
                    </a>
                </div>
            </div>

            {{-- Alumnos y Pagos: solo en año actual --}}
            @if (!$modoLectura)

            {{-- Alumnos (colapsable) --}}
            <div x-data="{ open: {{ request()->routeIs('alumnos.*') || request()->routeIs('apoderados.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-[13px] font-medium transition-colors duration-150
                               {{ request()->routeIs('alumnos.*') || request()->routeIs('apoderados.*') ? 'bg-primary-darker text-white' : 'text-white/70 hover:bg-primary-dark hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-user-graduate w-4 text-center text-[11px]"></i>
                        Alumnos
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200 flex-shrink-0"
                       :class="open ? 'rotate-90' : ''"></i>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 ml-3 pl-3 border-l border-primary-dark space-y-0.5">
                    <a href="{{ route('alumnos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('alumnos.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-users w-3.5 text-center text-[10px]"></i>
                        Lista de Alumnos
                    </a>
                    <a href="{{ route('apoderados.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('apoderados.*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-people-roof w-3.5 text-center text-[10px]"></i>
                        Apoderados
                    </a>
                    <a href="{{ route('alumnos.promover') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('alumnos.promover*') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-arrow-up-right-dots w-3.5 text-center text-[10px]"></i>
                        Promover Alumnos
                    </a>
                </div>
            </div>

            {{-- Pagos (colapsable) --}}
            <div x-data="{ open: {{ request()->routeIs('pagos.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-[13px] font-medium transition-colors duration-150
                               {{ request()->routeIs('pagos.*') ? 'bg-primary-darker text-white' : 'text-white/70 hover:bg-primary-dark hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-money-bill-wave w-4 text-center text-[11px]"></i>
                        Pagos
                    </span>
                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200 flex-shrink-0"
                       :class="open ? 'rotate-90' : ''"></i>
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 ml-3 pl-3 border-l border-primary-dark space-y-0.5">
                    <a href="{{ route('pagos.matricular') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('pagos.matricular') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-file-signature w-3.5 text-center text-[10px]"></i>
                        Matricular Alumno
                    </a>
                    <a href="{{ route('pagos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-150
                              {{ request()->routeIs('pagos.index') ? 'bg-primary-darker text-white' : 'text-white/60 hover:bg-primary-dark hover:text-white' }}">
                        <i class="fa-solid fa-receipt w-3.5 text-center text-[10px]"></i>
                        Registrar Pago
                    </a>
                </div>
            </div>

            @endif {{-- fin !$modoLectura --}}

            {{-- Reportes --}}
            <a href="{{ route('reportes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-colors duration-150
                      {{ request()->routeIs('reportes.*') ? 'bg-primary-darker text-white' : 'text-white/70 hover:bg-primary-dark hover:text-white' }}">
                <i class="fa-solid fa-chart-column w-4 text-center text-[11px]"></i>
                Reportes
            </a>

        </nav>

        {{-- Usuario logueado --}}
        <div class="px-4 py-3 border-t border-primary-dark flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-darker flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user text-white/70 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-white/40 text-[10px]">Administrador</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Cerrar sesión"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-white/40 hover:text-white hover:bg-primary-dark transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>
    {{-- ===== FIN SIDEBAR ===== --}}

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Navbar superior --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3.5 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-gray-900 font-semibold text-lg leading-tight">
                    @yield('page-title', 'Dashboard')
                </h1>
                @hasSection('breadcrumb')
                    <p class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @yield('page-actions')
            </div>
        </header>

        {{-- Área de contenido --}}
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">

            {{-- Banner modo lectura --}}
            @if ($modoLectura)
                <div class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                    <i class="fa-solid fa-eye text-amber-500 flex-shrink-0 mt-0.5"></i>
                    <div class="text-sm text-amber-700">
                        <span class="font-semibold">Modo lectura — Año {{ $anioActivo->nombre }}.</span>
                        Solo puedes consultar reportes y configuración.
                        Para volver al año actual, activa el año {{ $anioActual }} en
                        <a href="{{ route('configuracion.anios.index') }}" class="font-semibold underline">Configuración → Años</a>.
                    </div>
                </div>
            @endif

            {{-- Flash: success --}}
            @if (session('success'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-green-500 flex-shrink-0"></i>
                    <span class="text-sm text-green-700 flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- Flash: error --}}
            @if (session('error'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0"></i>
                    <span class="text-sm text-red-700 flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- Flash: warning --}}
            @if (session('warning'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-5 flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 flex-shrink-0"></i>
                    <span class="text-sm text-amber-700 flex-1">{{ session('warning') }}</span>
                    <button @click="show = false" class="text-amber-400 hover:text-amber-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            @endif

            @yield('content')

        </main>

    </div>
    {{-- ===== FIN CONTENIDO PRINCIPAL ===== --}}

</div>

@stack('scripts')
</body>
</html>
