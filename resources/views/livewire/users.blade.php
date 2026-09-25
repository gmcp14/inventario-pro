<div>
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Gestión de Usuarios y Roles</h1>
            <p class="text-xs text-slate-500">Administra los accesos y roles asignados al personal</p>
        </div>
        <button wire:click="openModal" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition flex items-center gap-2 w-fit">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span>Nuevo Usuario</span>
        </button>
    </div>

    <!-- Buscador y Filtros -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[20px]">search</span>
            <input type="text" wire:model.live="search" placeholder="Buscar por nombre o correo electrónico..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Usuario</th>
                        <th class="py-3 px-4">Correo Electrónico</th>
                        <th class="py-3 px-4 text-center">Rol Asignado</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($users as $user)
                        <tr>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $user->name }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $user->email }}</td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $roleName = $user->roles->first()?->name ?? 'Sin Rol';
                                    $badgeColor = match($roleName) {
                                        'Administrador' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Cajero' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Almacenista' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-50 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
    <button wire:click="toggleStatus({{ $user->id }})" 
            class="px-2.5 py-1 rounded-full text-[10px] font-bold border transition flex items-center gap-1 mx-auto {{ $user->estado ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
        <span class="w-1.5 h-1.5 rounded-full {{ $user->estado ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
        <span>{{ $user->estado ? 'Activo' : 'Inactivo' }}</span>
    </button>
</td>
                            <td class="py-3 px-4 text-right">
                                <button wire:click="editUser({{ $user->id }})" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">No se encontraron usuarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL REGISTRO / EDICIÓN -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-slate-100 shadow-xl">
                <h2 class="text-base font-bold text-slate-800 mb-4">
                    {{ $userId ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </h2>

                <form wire:submit.prevent="saveUser" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre Completo</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                        @error('name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Correo Electrónico</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                        @error('email') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Contraseña {{ $userId ? '(Dejar en blanco para conservar)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                        @error('password') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Rol de Acceso</label>
                        <select wire:model="role" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Selecciona un Rol</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="estado" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <span class="text-xs font-semibold text-slate-700">Usuario Activo (Permite iniciar sesión)</span>
                        </label>
                        @error('estado') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100 transition">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>