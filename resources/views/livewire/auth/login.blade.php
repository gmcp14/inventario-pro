<div class="flex flex-col w-full relative min-h-full items-center justify-center p-space-md">
    <!-- Immersive Logistics Warehouse Background -->
    <div class="fixed inset-0 w-full h-full bg-cover bg-center bg-no-repeat transition-all duration-700" style="background-image: url('img/screen.png');">
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/20 via-indigo-950/10 to-transparent"></div>
        <div class="absolute inset-0 backdrop-blur-[0.5px] bg-slate-900/10"></div>
    </div>

    <!-- Ambient Glow Behind Card -->
    <div class="absolute w-96 h-96 bg-gradient-to-br from-indigo-500/30 to-blue-400/25 rounded-full blur-3xl pointer-events-none -top-12"></div>

    <!-- Glassmorphic Central Card Container -->
    <div class="relative w-full max-w-auth-card-max-width rounded-2xl shadow-2xl p-space-lg text-slate-900 overflow-hidden" style="background: rgba(255, 255, 255, 0.90); border: 1px solid rgba(255, 255, 255, 0.95); backdrop-filter: blur(24px); box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 40px rgba(99, 102, 241, 0.25);">
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-600"></div>

        <!-- Header Section -->
        <div class="flex flex-col items-center text-center mb-space-lg pt-1">
            <div class="relative mb-3 group">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-2xl blur-md opacity-40 group-hover:opacity-70 transition-opacity"></div>
                <div class="relative w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 p-2.5">
                    <span class="material-symbols-outlined text-[32px]">inventory_2</span>
                </div>
            </div>
            
            <h1 class="text-[28px] font-bold tracking-tight text-slate-900 leading-tight">Inventario Pro</h1>
            <p class="text-[14px] text-slate-500 font-medium tracking-normal mt-0.5">Gestión Inteligente de Ventas e Inventario</p>
            <div class="mt-3 px-3 py-1 rounded-full bg-slate-100/90 border border-slate-200/70 shadow-inner">
                <p class="text-[12px] font-medium text-slate-600">Ingresa con tus credenciales corporativas</p>
            </div>
        </div>

        <!-- Form Section con Livewire -->
        <form wire:submit.prevent="login" class="flex flex-col gap-space-md" id="loginForm" novalidate>
            <!-- Email Field -->
            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-slate-700 font-medium flex justify-between items-center" for="email">
                    <span>Correo Electrónico</span>
                    
                </label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[20px]">mail</span>
                    <input wire:model="email" autocomplete="email" class="w-full h-11 pl-10 pr-4 bg-white text-slate-900 font-body-md text-body-md rounded-lg border border-slate-200 shadow-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('email') border-red-500 @enderror" id="email" type="email" placeholder="usuario@novastock.internal"/>
                </div>
                @error('email')
                    <span class="text-xs text-red-500 font-medium mt-0.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="flex flex-col gap-1.5" x-data="{ showPassword: false }">
                <div class="flex items-center justify-between">
                    <label class="font-label-md text-label-md text-slate-700 font-medium" for="password">Contraseña</label>
                    <a class="font-label-sm text-label-sm text-indigo-600 hover:text-indigo-800 font-semibold transition-colors" href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[20px]">lock</span>
                    <input wire:model="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" class="w-full h-11 pl-10 pr-24 bg-white text-slate-900 font-body-md text-body-md rounded-lg border border-slate-200 shadow-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all @error('password') border-red-500 @enderror" id="password" placeholder="••••••••••••"/>
                    <button @click="showPassword = !showPassword" aria-label="Alternar visibilidad de contraseña" class="absolute right-1.5 h-8 px-2.5 flex items-center gap-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-all border border-slate-200/80" type="button">
                        <span class="material-symbols-outlined text-[16px] text-slate-500" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                        <span class="font-label-sm text-label-sm select-none font-medium" x-text="showPassword ? 'Ocultar' : 'Mostrar'">Mostrar</span>
                    </button>
                </div>
                @error('password')
                    <span class="text-xs text-red-500 font-medium mt-0.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me & Security Badge -->
           

            <!-- Action Button con Livewire Loading State -->
            <button wire:loading.attr="disabled" class="relative w-full h-11 mt-2 rounded-lg bg-gradient-to-r from-indigo-600 via-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 active:scale-[0.99] text-white font-semibold text-[15px] shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 transition-all overflow-hidden cursor-pointer group disabled:opacity-50" type="submit">
                <span wire:loading.remove wire:target="login" class="tracking-wide">Iniciar Sesión</span>
                <span wire:loading.remove wire:target="login" class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                    </svg>
                    <span>Autenticando...</span>
                </span>
            </button>
        </form>
    </div>
</div>