<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Inventario Pro - Panel Principal' }}</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('img/screen.ico')}}">


    <!-- Fuentes y Iconos Google Material Symbols -->
    <link rel="stylesheet" href="{{asset('css/fonts-googleapis.css')}}">
    <link rel="stylesheet" href="{{asset('css/fonts-google2.css')}}">
    <script src="{{asset('sweetalert2@11')}}"></script>

    @include('estilos.plantilla-estilo')

    @livewireStyles
</head>

<body class="bg-[#f8fafc] font-body-md text-slate-800 flex min-h-screen antialiased">

    <!-- OVERLAY / BACKDROP PARA MÓVILES -->
    <div class="fixed inset-0 bg-black/60 z-40 hidden transition-opacity duration-300 backdrop-blur-xs lg:hidden"
        id="sidebar-backdrop"></div>

    <!-- SIDEBAR / MENÚ LATERAL -->
    <aside
        class="fixed top-0 left-0 bottom-0 z-50 w-64 h-screen flex flex-col justify-between bg-white border-r border-slate-200/80 shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
        id="sidebar-drawer">
        <div class="flex flex-col p-4 overflow-y-auto">
            <!-- Header Marca -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div
                        class="h-9 w-9 rounded-xl flex items-center justify-center bg-indigo-600 shadow-md ring-2 ring-indigo-100 shrink-0">
                        <span class="material-symbols-outlined text-[20px] text-white">inventory_2</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold tracking-tight text-slate-900 text-base">Inventario<span
                                class="text-indigo-600">Pro</span></span>
                        <span class="font-data-mono text-[10px] text-indigo-600 font-semibold">v3.4.1 Cloud</span>
                    </div>
                </div>
                <button aria-label="Cerrar menú"
                    class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                    id="mobile-close-btn" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Navegación -->
            <nav class="flex flex-col gap-1.5">
                <!-- Dashboard: Accesible para TODOS los roles autenticados -->
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 font-medium' }}"
                    href="{{ route('dashboard') }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[20px] {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500' }}">dashboard</span>
                        <span class="text-sm">Inicio / Dashboard</span>
                    </div>
                </a>

                <!-- MÓDULO DE INVENTARIO Y COMPRAS: Administrador y almacenista -->
                @hasanyrole('Administrador|Almacenista')
                 <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Inventario & Compras
                </div>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl  transition->all {{request()->routeIs('categorias') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50 font-medium' }}"
                    href="{{route('categorias')}}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('categorias') ? 'text-white' : 'text-sky-600' }}">category</span>
                        <span class="text-sm">Categoría</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{request()->routeIs('productos') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-amber-400 hover:bg-amber-50 font-medium' }}"
                    href="{{route('productos')}}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('productos') ? 'text-white' : 'text-amber-500' }}">inventory_2</span>
                        <span class="text-sm">Productos</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{request()->routeIs('Kardex') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 font-medium' }}"
                    href="{{ route('Kardex') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('Kardex') ? 'text-white' : 'text-indigo-600' }}">swap_vert</span>
                        <span class="text-sm">Kárdex e Inventario</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{ request()->routeIS('Suppliers') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 font-medium '}}"
                    href="{{ route('Suppliers') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIS('Suppliers')  ? 'text-white' : 'text-emerald-600' }}">local_shipping</span>
                        <span class="text-sm">Proveedores</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{ request()->routeIS('purchases') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 font-medium '}}"
                    href="{{ route('purchases') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px]  {{ request()->routeIS('purchases')  ? 'text-white' : 'text-indigo-700' }}">shopping_bag</span>
                        <span class="text-sm">Compras / Entradas</span>
                    </div>
                </a>
                @endhasanyrole

                <!-- MÓDULO DE VENTAS Y CAJA: Administrador y cajero -->
                 @hasanyrole('Administrador|Cajero')
                 <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Ventas & Clientes
                </div>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{request()->routeIs('pos') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-amber-600 hover:bg-amber-50 font-medium '}} "
                    href="{{route('pos')}}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('pos') ? 'text-white' : 'text-amber-600' }}">point_of_sale</span>
                        <span class="text-sm">Punto de Venta (POS)</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl {{request()->routeIs('sales') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-rose-600 hover:bg-rose-50 font-medium '}}"
                    href="{{ route('sales') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('sales') ? 'text-white' : 'text-rose-600' }}">receipt_long</span>
                        <span class="text-sm">Historial de Venta</span>
                    </div>
                </a>
                <a class="flex items-center justify-between px-3 py-2.5 rounded-xl  transition->all {{request()->routeIs('clientes') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-sky-500 hover:bg-sky-50 font-medium' }}""
                    href="{{route('clientes')}}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('clientes') ? 'text-white' : 'text-sky-500' }}">inventory_2</span>
                        <span class="text-sm">Clientes</span>
                    </div>
                </a>
               @endhasanyrole
                
                <!-- MÓDULO DE UUSARIOS:Exclusivo de administardor -->
                @role('Administrador')
                <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Administración
                </div>
                    <a class="flex items-center justify-between px-3 py-2.5 rounded-xl transition->all {{ request()->routeIS('users') ? 'bg-indigo-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 font-medium '}}"
                    href="{{ route('users') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[20px] {{ request()->routeIS('users')  ? 'text-white' : 'text-emerald-700' }}">group</span>
                        <span class="text-sm">Usuarios y Roles</span>
                    </div>
                </a>
                
                @endrole
            </nav>
        </div>

        <!-- Perfil y Logout -->
        <div class="p-3 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Usuario' }}</span>
                        <span
                            class="text-[10px] font-semibold text-indigo-600 truncate">{{ auth()->user()->roles->first()?->name ?? 'Usuario' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{route('logout')}}">
                    @csrf
                    <button
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                        title="Cerrar Sesión" type="submit">
                        <span class="material-symbols-outlined text-[17px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 min-h-screen flex flex-col bg-[#f8fafc] w-full lg:ml-64 transition-all duration-300">
        <!-- HEADER SUPERIOR -->
        <header
            class="sticky top-0 z-30 h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2">
                <button aria-label="Abrir menú"
                    class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100"
                    id="mobile-menu-btn" type="button">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>
                <span class="text-sm font-semibold text-slate-700">Sistema de Inventario</span>
            </div>
        </header>

        <!-- AQUÍ SE INYECTA EL CONTENIDO DE dashboard.blade.php O CUALQUIER OTRA VISTA DE LA APP -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 flex flex-col gap-6 w-full max-w-7xl mx-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

    <!-- SCRIPT DE NAVEGACIÓN MÓVIL DE SIDEBAR -->
    <script>
    (function() {
        const drawer = document.getElementById('sidebar-drawer');
        const backdrop = document.getElementById('sidebar-backdrop');
        const openBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('mobile-close-btn');

        function openSidebar() {
            if (drawer) drawer.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            if (drawer) drawer.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    })();



    document.addEventListener('livewire:init', () => {
        // Alerta Éxito
        Livewire.on('swal:success', (data) => {
            Swal.fire({
                title: data.title,
                text: data.text,
                icon: 'success',
                confirmButtonColor: '#4f46e5',
                background: '#ffffff', // Fondo blanco
                color: '#0f172a', // Texto oscuro (Slate 900)
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    confirmButton: 'px-5 py-2.5 rounded-lg text-sm font-semibold'
                }
            });
        });

        // Alerta Error
        Livewire.on('swal:error', (data) => {
            Swal.fire({
                title: data.title,
                text: data.text,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
    });
    </script>
</body>

</html>