<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — I.E.P. Jesús y María</title>
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.6.0-web/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-cream flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Cabecera verde --}}
            <div class="bg-primary px-8 py-8 flex flex-col items-center gap-3">
                {{-- Logo con tamaño garantizado --}}
                <div class="w-20 h-20 flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('img/logo_colegio/logocolegio.png') }}"
                         alt="Logo I.E.P. Jesús y María"
                         style="width:80px;height:80px;object-fit:contain;">
                </div>
                <div class="text-center">
                    <p class="text-white/70 text-[11px] font-medium uppercase tracking-widest">
                        Institución Educativa Privada
                    </p>
                    <h1 class="text-white font-bold text-2xl leading-tight mt-0.5">Jesús y María</h1>
                    <p class="text-gold text-xs font-semibold mt-1">Sistema de Gestión Escolar</p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="px-8 py-7">
                <h2 class="text-center text-gray-600 text-sm font-medium mb-6">
                    Ingresa tus credenciales para continuar
                </h2>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-red-700 text-sm">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   autocomplete="email"
                                   required
                                   placeholder="correo@ejemplo.com"
                                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800
                                          placeholder-gray-400 transition-colors
                                          focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20
                                          @error('email') border-red-400 bg-red-50 @enderror">
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Contraseña
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   autocomplete="current-password"
                                   required
                                   placeholder="••••••••"
                                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800
                                          placeholder-gray-400 transition-colors
                                          focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    {{-- Recordar sesión --}}
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox"
                               id="remember"
                               name="remember"
                               class="w-4 h-4 rounded border-gray-300 accent-primary cursor-pointer">
                        <label for="remember" class="text-sm text-gray-500 cursor-pointer select-none">
                            Recordar sesión
                        </label>
                    </div>

                    {{-- Botón --}}
                    <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark active:bg-primary-darker
                                   text-white font-semibold py-3 rounded-xl
                                   flex items-center justify-center gap-2 text-sm
                                   transition-colors duration-200 shadow-md mt-1">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Ingresar al sistema
                    </button>

                </form>
            </div>
        </div>

        {{-- Pie --}}
        <p class="text-center text-xs text-gray-500 mt-5">
            &copy; {{ date('Y') }} I.E.P. Jesús y María — Sistema de Gestión Escolar
        </p>
    </div>

</body>
</html>
