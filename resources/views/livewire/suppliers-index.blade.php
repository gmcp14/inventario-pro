<div>
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Directorio de Proveedores</h1>
            <p class="text-xs text-slate-500">Gestión de distribuidores y contactos comerciales</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nuevo Proveedor
        </button>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="relative w-full sm:w-80">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, RIF o contacto..." 
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <!-- Tabla de Proveedores -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Documento</th>
                        <th class="py-3.5 px-6">Empresa / Nombre</th>
                        <th class="py-3.5 px-6">Contacto</th>
                        <th class="py-3.5 px-6">Teléfono / Email</th>
                        <th class="py-3.5 px-6 text-center">Estado</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-800">
                                {{ $supplier->full_document }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-800">
                                {{ $supplier->name }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                {{ $supplier->contact_person ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">
                                <div>{{ $supplier->phone ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $supplier->email ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($supplier->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Activo</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <button wire:click="edit({{ $supplier->id }})" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Editar">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">local_shipping</span>
                                <p>No se encontraron proveedores registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- MODAL REGISTRO / EDICIÓN -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-800">{{ $supplierId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>

                <form wire:submit.prevent="save" class="space-y-3">
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tipo</label>
                            <select wire:model="document_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                                <option value="RIF">RIF</option>
                                <option value="NIT">NIT</option>
                                <option value="V">V</option>
                                <option value="J">J</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1">Número Documento</label>
                            <input wire:model="document_number" type="text" placeholder="J-12345678-0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            @error('document_number') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nombre / Razón Social</label>
                        <input wire:model="name" type="text" placeholder="Ej: Distribuidora Central C.A." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                        @error('name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Persona de Contacto</label>
                            <input wire:model="contact_person" type="text" placeholder="Ej: Carlos Pérez" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Teléfono</label>
                            <input wire:model="phone" type="text" placeholder="04121234567" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Correo Electrónico</label>
                        <input wire:model="email" type="email" placeholder="ventas@proveedor.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Dirección</label>
                        <textarea wire:model="address" rows="2" placeholder="Dirección fiscal o de almacén" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input wire:model="is_active" type="checkbox" id="supplier_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <label for="supplier_active" class="text-xs font-medium text-slate-700">Proveedor Activo</label>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>