<div>
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Kárdex y Movimientos de Inventario</h1>
            <p class="text-xs text-slate-500">Historial trazable de entradas, salidas y ajustes de stock</p>
        </div>
        <button wire:click="openAdjustmentModal" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">tune</span> Ajuste Manual de Stock
        </button>
        <a href="{{ route('reports.kardex-pdf') }}" target="_blank" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
    <span>Exportar PDF</span>
</a>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Filtrar por Producto</label>
                <select wire:model.live="selectedProductId" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="">Todos los Productos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Tipo de Movimiento</label>
                <select wire:model.live="movementType" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="all">Todos los Tipos</option>
                    <option value="in">Entrada</option>
                    <option value="out">Salida</option>
                    <option value="adjustment">Ajuste Manual</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Desde</label>
                <input wire:model.live="dateFrom" type="date" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Hasta</label>
                <input wire:model.live="dateTo" type="date" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Ficha de Resumen del Producto Seleccionado (Si aplica) -->
    @if($selectedProduct)
        <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 font-mono">{{ $selectedProduct->code }}</span>
                <h2 class="text-base font-bold text-indigo-950">{{ $selectedProduct->name }}</h2>
            </div>
            <div class="flex items-center gap-6 text-xs">
                <div>
                    <span class="text-slate-400 block">Stock Actual:</span>
                    <span class="font-mono font-extrabold text-indigo-700 text-lg">{{ $selectedProduct->stock }} u.</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Precio Costo:</span>
                    <span class="font-mono font-bold text-slate-700">${{ number_format($selectedProduct->cost_price, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Precio Venta:</span>
                    <span class="font-mono font-bold text-emerald-600">${{ number_format($selectedProduct->sale_price, 2) }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Kárdex -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Fecha / Hora</th>
                        <th class="py-3.5 px-6">Producto</th>
                        <th class="py-3.5 px-6 text-center">Tipo</th>
                        <th class="py-3.5 px-6 text-center">Stock Anter.</th>
                        <th class="py-3.5 px-6 text-center">Cantidad</th>
                        <th class="py-3.5 px-6 text-center">Nuevo Stock</th>
                        <th class="py-3.5 px-6">Descripción / Referencia</th>
                        <th class="py-3.5 px-6">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($movements as $mov)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono text-slate-500">
                                {{ $mov->created_at->format('d/m/Y h:i A') }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-800">
                                {{ $mov->product->name }}
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $mov->product->code }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($mov->type === 'in')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Entrada</span>
                                @elseif($mov->type === 'out')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">Salida</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">Ajuste</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono text-slate-500">
                                {{ $mov->previous_stock }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono font-extrabold {{ $mov->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mov->quantity > 0 ? '+' . $mov->quantity : $mov->quantity }}
                            </td>
                            <td class="py-3.5 px-6 text-center font-mono font-bold text-slate-800">
                                {{ $mov->new_stock }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                {{ $mov->description }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">
                                {{ $mov->user->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">swap_vert</span>
                                <p>No hay movimientos registrados en el Kárdex.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $movements->links() }}
        </div>
    </div>

    <!-- MODAL AJUSTE MANUAL -->
    @if($showAdjustmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-800">Ajuste Manual de Inventario</h3>
                    <button wire:click="closeAdjustmentModal" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>

                <form wire:submit.prevent="saveAdjustment" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Producto *</label>
                        <select wire:model="adj_product_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="">Seleccionar Producto...</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Stock actual: {{ $prod->stock }})</option>
                            @endforeach
                        </select>
                        @error('adj_product_id') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tipo de Ajuste</label>
                            <select wire:model="adj_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                                <option value="in">Entrada (+ Sumar)</option>
                                <option value="out">Salida (- Restar)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Cantidad *</label>
                            <input wire:model="adj_quantity" type="number" min="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            @error('adj_quantity') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Motivo / Descripción *</label>
                        <textarea wire:model="adj_description" rows="3" placeholder="Ej: Mercancía dañada en almacén, conteo físico, etc." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500"></textarea>
                        @error('adj_description') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeAdjustmentModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors">Guardar Ajuste</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>