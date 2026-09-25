<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
    <title>{{ $title ?? 'Inventario Pro - Login' }}</title>
     <link rel="icon" type="image/png" sizes="16x16" href="{{asset('img/screen.ico')}}">
    
    <!-- Fuentes e Íconos -->
     <link rel="stylesheet" href="{{asset('css/fonts-googleapis.css')}}">
     <link rel="stylesheet" href="{{asset('css/fonts-google2.css')}}">
      
    <!-- SweetAlert2 -->
   
    <script src="{{asset('sweetalert2@11')}}"></script>
     @include('estilos.login-estilo')
    
    @livewireStyles
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface flex flex-col min-h-screen relative antialiased selection:bg-primary-container selection:text-on-primary-container">
    <main class="flex flex-col relative w-full pt-safe pb-safe bg-surface flex-grow justify-center min-h-screen">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
       window.addEventListener('swal:modal', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.type,
                confirmButtonColor: '#4f46e5', // Color índigo a juego con el botón principal
                background: '#ffffff',          // Fondo blanco
                color: '#0f172a',               // Texto oscuro (Slate 900)
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    confirmButton: 'px-5 py-2.5 rounded-lg text-sm font-semibold'
                }
            });
        });
    </script>
</body>
</html>