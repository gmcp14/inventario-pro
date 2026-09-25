<div>
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">Directorio de Clientes</h1>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-semibold border border-indigo-100">
                    {{ $totalCustomers }} registrados
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Administra la información de tus clientes para la emisión de ventas y comprobantes
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="create" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-200">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                + Nuevo Cliente
            </button>
        </div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <!-- Total Clientes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-indigo-600 text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">TOTAL CLIENTES</p>
            <span class="text-3xl font-extrabold text-slate-800">{{ $totalCustomers }}</span>
        </div>

        <!-- Clientes Activos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-emerald-500 text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">CLIENTES ACTIVOS</p>
            <span class="text-3xl font-extrabold text-slate-800">{{ $activeCustomers }}</span>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Input de Búsqueda -->
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, CI/RIF, teléfono..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>

        <!-- Filtro por Estado -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <select wire:model.live="statusFilter" class="bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 py-2 px-3 focus:outline-none focus:border-indigo-500">
                <option value="all">Todos los Estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">DOCUMENTO</th>
                        <th class="py-3.5 px-6">NOMBRE / RAZÓN SOCIAL</th>
                        <th class="py-3.5 px-6">TELÉFONO</th>
                        <th class="py-3.5 px-6">CORREO ELECTRÓNICO</th>
                        <th class="py-3.5 px-6 text-center">ESTADO</th>
                        <th class="py-3.5 px-6 text-right">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Documento (CI / RIF) -->
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-mono rounded-lg text-xs font-semibold border border-slate-200">
                                    {{ $customer->full_document }}
                                </span>
                            </td>

                            <!-- Nombre -->
                            <td class="py-3.5 px-6">
                                <p class="font-semibold text-slate-800">{{ $customer->name }}</p>
                                @if($customer->address)
                                    <p class="text-xs text-slate-400 truncate max-w-xs" title="{{ $customer->address }}">{{ $customer->address }}</p>
                                @endif
                            </td>

                            <!-- Teléfono -->
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-600">
                                {{ $customer->phone ?? '—' }}
                            </td>

                            <!-- Correo -->
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                {{ $customer->email ?? '—' }}
                            </td>

                            <!-- Estado -->
                            <td class="py-3.5 px-6 text-center">
    <button wire:click="toggleStatus({{ $customer->id }})" 
            class="transition-transform active:scale-95 focus:outline-none" 
            title="Haz clic para cambiar estado">
        @if($customer->is_active)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100">
                Activo
            </span>
        @else
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200 hover:bg-slate-200">
                Inactivo
            </span>
        @endif
    </button>
</td>
                            <!-- Acciones -->
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $customer->id }})" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    @if($customer->document_number !== '00000000')
                                        <button type="button" onclick="confirmDeleteCustomer({{ $customer->id }})" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                No se encontraron clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="p-4 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Modal Formulario -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800">
                        {{ $customerId ? 'Editar Cliente' : 'Nuevo Cliente' }}
                    </h2>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 rounded-lg p-1">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Tipo y Número de Documento -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Tipo *</label>
                            <select wire:model="document_type" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                                <option value="V">V - Venezolano</option>
                                <option value="E">E - Extranjero</option>
                                <option value="J">J - Jurídico</option>
                                <option value="G">G - Gubernamental</option>
                                <option value="P">P - Pasaporte</option>
                            </select>
                            @error('document_type') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Documento / C.I. / RIF *</label>
                            <input wire:model="document_number" type="text" placeholder="Ej. 12345678" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('document_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Nombre / Razón Social -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nombre o Razón Social *</label>
                        <input wire:model="name" type="text" placeholder="Ej. Juan Pérez / Empresa C.A." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Correo y Teléfono -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Teléfono</label>
                            <input wire:model="phone" type="text" placeholder="Ej. 04121234567" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Correo Electrónico</label>
                            <input wire:model="email" type="email" placeholder="cliente@correo.com" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Dirección Física</label>
                        <textarea wire:model="address" rows="2" placeholder="Ubicación o dirección fiscal..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500"></textarea>
                        @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Estado -->
                   <div class="flex items-center gap-2 pt-1">
    <input wire:model="is_active" type="checkbox" id="customer_is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
    <label for="customer_is_active" class="text-sm font-medium text-slate-700">Cliente Activo para Facturación</label>
</div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm shadow-indigo-200 flex items-center gap-2">
                            <span wire:loading wire:target="save" class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></span>
                            {{ $customerId ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Script SweetAlert2 para Confirmar Eliminación -->
<script>
    function confirmDeleteCustomer(id) {
        Swal.fire({
            title: '¿Eliminar cliente?',
            text: "Esta acción borrará el registro del cliente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl text-xs font-semibold px-4 py-2.5',
                cancelButton: 'rounded-xl text-xs font-semibold px-4 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        });
    }
</script>