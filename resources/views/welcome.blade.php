<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Laravel - Frontend</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e293b',   // Color oscuro del menú
                        accent: '#3b82f6',    // Color de acento (azul)
                        hover: '#334155',     // Color al pasar el mouse
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Evita que los elementos parpadeen antes de cargar Alpine */
        [x-cloak] { display: none !important; }
        
        /* Personalización de la barra de desplazamiento para el menú */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #1e293b; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-primary text-white transition-transform duration-300 transform lg:static lg:translate-x-0 flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex items-center justify-center h-16 bg-slate-900 border-b border-slate-700 shadow-md flex-shrink-0">
                <div class="flex items-center font-bold text-xl tracking-wider">
                    <svg class="w-8 h-8 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    MI SISTEMA
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto sidebar-scroll">
                
                <a href="#" class="flex items-center px-4 py-3 text-gray-100 bg-accent rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</p>
                </div>

                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="flex items-center justify-between w-full px-4 py-3 text-gray-300 rounded-lg hover:bg-hover hover:text-white transition-colors focus:outline-none">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-medium">Ventas</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-1">
                        <a href="#" class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-slate-800 transition-colors">Nueva Venta</a>
                        <a href="#" class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-slate-800 transition-colors">Historial</a>
                        <a href="#" class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-slate-800 transition-colors">Reportes</a>
                    </div>
                </div>

                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="flex items-center justify-between w-full px-4 py-3 text-gray-300 rounded-lg hover:bg-hover hover:text-white transition-colors focus:outline-none">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="font-medium">Usuarios</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-1">
                        <a href="#" class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-slate-800 transition-colors">Lista de Usuarios</a>
                        <a href="#" class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-slate-800 transition-colors">Roles y Permisos</a>
                    </div>
                </div>

            </nav>

            <div class="border-t border-slate-700 p-4 bg-slate-900 flex-shrink-0">
                <a href="#" class="flex items-center w-full hover:bg-hover p-2 rounded-md transition-colors">
                    <div class="w-8 h-8 rounded-full bg-accent text-white flex items-center justify-center font-bold">
                        AD
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">Admin</p>
                        <p class="text-xs text-gray-400">Ver Perfil</p>
                    </div>
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4 lg:ml-0">Dashboard</h2>
                </div>

                <div class="flex items-center gap-4">
                    <button class="text-gray-500 hover:text-accent transition-colors relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">3</span>
                    </button>
                    <button class="text-sm font-medium text-gray-600 hover:text-red-500 transition-colors">
                        Cerrar Sesión
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                
                <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
                    <div class="w-full px-4 py-5 bg-white rounded-lg shadow">
                        <div class="text-sm font-medium text-gray-500 truncate">Total Usuarios</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">12,000</div>
                    </div>
                    <div class="w-full px-4 py-5 bg-white rounded-lg shadow">
                        <div class="text-sm font-medium text-gray-500 truncate">Ingresos Hoy</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">S/. 5,400</div>
                    </div>
                    <div class="w-full px-4 py-5 bg-white rounded-lg shadow">
                        <div class="text-sm font-medium text-gray-500 truncate">Nuevos Pedidos</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">45</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-8 min-h-[400px]">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Contenido del Sistema</h3>
                    <p class="text-gray-600">Aquí puedes inyectar tus vistas antiguas...</p>
                </div>

            </main>
        </div>

        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden">
        </div>

    </div>
</body>
</html>