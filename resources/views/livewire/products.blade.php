<div>
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">Catálogo de Productos</h1>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-semibold border border-indigo-100">
                    {{ $totalProducts }} registrados
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Controla el inventario, precios, stock crítico y categorías en tiempo real
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="create" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-indigo-200">
                <span class="material-symbols-outlined text-[20px]">add</span>
                 Nuevo Producto
            </button>
        </div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <!-- Total Productos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-indigo-600 text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">TOTAL PRODUCTOS</p>
            <span class="text-3xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
        </div>

        <!-- Alerta de Stock Bajo -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 {{ $lowStockProducts > 0 ? 'bg-rose-500' : 'bg-emerald-500' }} text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </div>
                @if($lowStockProducts > 0)
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-600 text-xs font-bold rounded-md border border-rose-100">
                        ¡Atención requerida!
                    </span>
                @endif
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">STOCK BAJO / MÍNIMO</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold {{ $lowStockProducts > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                    {{ $lowStockProducts }}
                </span>
                <span class="text-xs text-slate-500 font-medium">por reponer</span>
            </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-emerald-500 text-white rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">PRODUCTOS ACTIVOS</p>
            <span class="text-3xl font-extrabold text-slate-800">{{ $activeProducts }}</span>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Input de Búsqueda -->
        <div class="relative w-full md:w-80">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por código, nombre..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
        </div>

        <!-- Filtros Desplegables -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
            <!-- Categoría -->
            <select wire:model.live="categoryFilter" class="bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 py-2 px-3 focus:outline-none focus:border-indigo-500">
                <option value="all">Todas las Categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Estado -->
            <select wire:model.live="statusFilter" class="bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 py-2 px-3 focus:outline-none focus:border-indigo-500">
                <option value="all">Todos los Estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">PRODUCTO</th>
                        <th class="py-3.5 px-6">CATEGORÍA</th>
                        <th class="py-3.5 px-6 text-right">COSTO</th>
                        <th class="py-3.5 px-6 text-right">PRECIO VENTA</th>
                        <th class="py-3.5 px-6 text-center">STOCK</th>
                        <th class="py-3.5 px-6 text-center">ESTADO</th>
                        <th class="py-3.5 px-6 text-right">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Producto: Imagen + Nombre + Código -->
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-400 text-[20px]">image</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 leading-tight">{{ $product->name }}</p>
                                        <p class="font-mono text-xs text-slate-400 mt-0.5">SKU: {{ $product->code }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Categoría -->
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-medium">
                                    {{ $product->category->name ?? 'Sin categoría' }}
                                </span>
                            </td>

                            <!-- Costo -->
                            <td class="py-3.5 px-6 text-right font-mono text-slate-500">
                                ${{ number_format($product->cost_price, 2) }}
                            </td>

                            <!-- Precio Venta -->
                            <td class="py-3.5 px-6 text-right font-mono font-bold text-slate-800">
                                ${{ number_format($product->sale_price, 2) }}
                            </td>

                            <!-- Stock con indicador de stock bajo -->
                            <td class="py-3.5 px-6 text-center">
                                @if($product->stock <= $product->min_stock)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        {{ $product->stock }} (Mín. {{ $product->min_stock }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        {{ $product->stock }} uds.
                                    </span>
                                @endif
                            </td>

                            <!-- Estado -->
                            <td class="py-3.5 px-6 text-center">
                                @if($product->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">Activo</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">Inactivo</span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $product->id }})" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button type="button" onclick="confirmDelete({{ $product->id }})" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                No se encontraron productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Formulario -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800">
                        {{ $productId ? 'Editar Producto' : 'Nuevo Producto' }}
                    </h2>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 rounded-lg p-1">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Categoría -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Categoría *</label>
                            <select wire:model="category_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Código / SKU -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Código / SKU *</label>
                            <input wire:model="code" type="text" placeholder="Ej. PROD-001" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('code') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Nombre del Producto -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nombre del Producto *</label>
                        <input wire:model="name" type="text" placeholder="Ej. Harina de Trigo 1Kg" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Precio Costo -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Precio Costo ($) *</label>
                            <input wire:model="cost_price" type="number" step="0.01" min="0" placeholder="0.00" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('cost_price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Precio Venta -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Precio Venta ($) *</label>
                            <input wire:model="sale_price" type="number" step="0.01" min="0" placeholder="0.00" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('sale_price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Stock Inicial -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Stock Actual *</label>
                            <input wire:model="stock" type="number" min="0" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('stock') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Stock Mínimo (Alerta) *</label>
                            <input wire:model="min_stock" type="number" min="0" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                            @error('min_stock') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Subida de Imagen y Previsualización -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Imagen del Producto</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($existing_image)
                                    <img src="{{ asset('storage/' . $existing_image) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-slate-400">image</span>
                                @endif
                            </div>
                            <input wire:model="image" type="file" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-colors">
                        </div>
                        @error('image') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Descripción</label>
                        <textarea wire:model="description" rows="2" placeholder="Detalles u observaciones del producto..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Estado -->
                    <div class="flex items-center gap-2 pt-1">
                        <input wire:model="is_active" type="checkbox" id="product_is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                        <label for="product_is_active" class="text-sm font-medium text-slate-700">Producto Activo para Venta</label>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm shadow-indigo-200 flex items-center gap-2">
                            <span wire:loading wire:target="save" class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></span>
                            {{ $productId ? 'Actualizar Producto' : 'Guardar Producto' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<!-- Script SweetAlert2 para Confirmar Eliminación -->
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: "Esta acción borrará el registro e imagen asociada.",
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