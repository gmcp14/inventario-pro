<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-slate-500 text-xs"><span>Inicio</span><span
                    class="material-symbols-outlined text-[14px]">chevron_right</span><span
                    class="text-indigo-600 font-semibold">Categorías</span></nav>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Gestión de Categorías</h1>
            </div>
            <p class="text-xs text-slate-500 m-2">Organiza y administra las familias de productos</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            
            <button wire:click="create"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-transform active:scale-95"
                 type="button"><span
                    class="material-symbols-outlined text-[18px]">add_circle</span><span>+ Nueva
                    Categoría</span></button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-5">
        <div
            class="p-5 rounded-2xl bg-white border border-indigo-100 flex flex-col justify-between hover:border-indigo-300 hover:shadow-md transition-all shadow-sm group">
            <div class="flex items-start justify-between mb-3">
                <div
                    class="w-11 h-11 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-600/30">
                    <span class="material-symbols-outlined text-[22px]">category</span>
                </div>
                @if($createdThisMonth > 0)
                <span
                    class="px-2.5 py-1 rounded-full bg-emerald-500 text-white font-data-mono text-xs font-bold shadow-xs shadow-emerald-500/20">+{{ $createdThisMonth }}
                    este mes</span>@endif
            </div>
            <div><span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Categorías</span>
                <div class="text-2xl sm:text-3xl font-bold text-slate-900 font-data-mono mt-1">{{ $totalCategories }} <span
                        class="text-xs font-medium text-slate-400 font-body-md">({{ $activeCategories }} activas)</span></div>
                <div class="flex items-center gap-1 mt-1 text-xs text-indigo-600 font-semibold"><span
                        class="material-symbols-outlined text-[14px]">verified</span><span>100% catalogadas</span></div>
            </div>
        </div>
       
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-emerald-500 text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-xs font-semibold rounded-lg border border-emerald-100">
                    En almacén
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">CATEGORÍAS INACTIVAS</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-800">{{ $inactiveCategories }}</span>
                <span class="text-xs text-slate-500 font-medium">pausadas</span>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                <span class="material-symbols-outlined text-[16px]">info</span>
                <span>Categorías no visibles en catálogo activo</span>
            </div>
        </div>
        
    </div>
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden flex flex-col mt-3">
        <div
            class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
            <div class="relative flex-1 max-w-md"><span
                    class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">search</span>
                <input wire:model.live="search"
                    class="w-full h-10 pl-9 pr-12 rounded-xl bg-white text-slate-800 placeholder:text-slate-400 text-xs sm:text-sm outline-none border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all"
                    placeholder="Buscar categoría por nombre, código o SKU..." type="text" /><kbd
                    class="absolute right-2.5 top-2.5 px-1.5 py-0.5 rounded bg-slate-100 font-data-mono text-[10px] text-slate-500 font-semibold border border-slate-200">⌘K</kbd>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-2.5 py-1.5"><span
                        class="text-xs text-slate-500 font-medium">Estado:</span>
                   <select wire:model.live="statusFilter" class="bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 py-2 px-3 focus:outline-none focus:border-indigo-500">
                <option value="all">Todas ({{ $totalCategories }})</option>
                <option value="active">Activas ({{ $activeCategories }})</option>
                <option value="inactive">Inactivas ({{ $inactiveCategories }})</option>
            </select>
                </div>
               
                
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead
                    class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                       
                        <th class="p-3.5">Categoría</th>
                        <th class="p-3.5">Slug</th>
                        <th class="p-3.5">Descripcion</th>
                        <th class="p-3.5">Estado</th>
                        <th class="p-3.5 pr-4 sm:pr-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                     @forelse ($categories as $category)
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                       <td class="p-3.5">{{$category->name}}</td>
                       <td class="p-3.5">{{ $category->slug }}</td>
                       <td class="p-3.5">{{ $category->description ?? '—' }}</td>
                       <td class="px-5 py-3.5">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Activa</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactiva</span>
                                @endif
                        </td>
                        <td class="p-3.5 pr-4 sm:pr-6 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button wire:click="edit({{$category->id}})"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                    title="Editar"><span
                                        class="material-symbols-outlined text-[16px]">edit</span></button>
                                <button onclick="confirmDelete({{ $category->id }})"" 
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                    title="Eliminar"><span
                                        class="material-symbols-outlined text-[16px]">delete</span></button>
                                                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                            No se enontraron categorías.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{$categories->links()}}
        </div>
        <!-- <div
            class="p-3.5 sm:p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs bg-slate-50/40">
            <div class="flex items-center gap-2 text-slate-500"><span>Mostrando</span><span
                    class="font-bold text-slate-800">1 - 7</span><span>de</span><span
                    class="font-bold text-slate-800">14</span><span>categorías totales</span></div>
            <div class="flex items-center justify-between sm:justify-end gap-3">
                <div class="flex items-center gap-1.5 text-slate-500"><span class="hidden sm:inline">Filas por
                        pág:</span>
                    <select
                        class="rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 bg-white px-2 py-1 outline-none">
                        <option>7</option>
                        <option>10</option>
                        <option>25</option>
                    </select>
                </div>
                <div class="flex items-center gap-1">
                    <button
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-400 text-xs font-semibold cursor-not-allowed"
                        disabled="" type="button">Anterior</button>
                    <button class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-xs"
                        type="button">1</button>
                    <button
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold"
                        type="button">2</button>
                    <button
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold"
                        type="button">Siguiente</button>
                </div>
            </div>
        </div> -->
    </div>
  @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4  transition-opacity"
      >
        <div
            class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/30">
                        <span class="material-symbols-outlined text-[20px]">category</span></div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900"> {{ $categoryId ? 'Editar Categoría' : 'Nueva Categoría'}}</h3>
                    </div>
                </div>
                <button wire:click="closeModal"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                    type="button"><span
                        class="material-symbols-outlined text-[20px]">close</span></button>
            </div>
            <div class="p-5 flex flex-col gap-4 text-xs">
                <form wire:submit.prevent="save" class="flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre</label>
                        <input wire:model="name" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none">
                        @error('name') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                        <textarea wire:model="description" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none"></textarea>
                        @error('description') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="text-xs text-slate-700 font-medium">Categoría Activa</label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-transform active:scale-95 flex items-center gap-1.5">
                            <span
                        class="material-symbols-outlined text-[16px]">save</span>Guardar
                        </button>
                    </div>
                </form>
               
            </div>
            
        </div>
    </div>
    @endif
</div>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
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
                // Ejecuta el método delete() en la clase Livewire pasando el ID
                @this.call('delete', id);
            }
        });
    }
</script>