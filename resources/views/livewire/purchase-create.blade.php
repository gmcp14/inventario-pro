<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Registrar Factura de Entrada / Compra</h1>
            <p class="text-xs text-slate-500">Ingreso de productos al inventario y actualización de costos</p>
        </div>
        <a href="{{ route('purchases') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
            Volver al Historial
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- FORMULARIO DE DETALLES Y PRODUCTOS (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Datos de Factura -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Datos del Proveedor y Factura</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Proveedor *</label>
                        <select wire:model="supplier_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="">Seleccionar Proveedor...</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->full_document }})</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nº Factura / Control *</label>
                        <input wire:model="invoice_number" type="text" placeholder="Ej: F-99820" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                        @error('invoice_number') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Forma de Pago</label>
                        <select wire:model="payment_method" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="transfer">Transferencia</option>
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta / Punto</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de Ítems Comprados -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Ítems de la Factura</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                <th class="py-2.5 px-3">Producto</th>
                                <th class="py-2.5 px-3 w-28 text-center">Cant. Ingresar</th>
                                <th class="py-2.5 px-3 w-32 text-right">Costo Unit. ($)</th>
                                <th class="py-2.5 px-3 w-32 text-right">Subtotal</th>
                                <th class="py-2.5 px-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($items as $id => $item)
                                <tr>
                                    <td class="py-3 px-3 font-bold text-slate-800">
                                        {{ $item['name'] }}
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $item['code'] }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" min="1" value="{{ $item['quantity'] }}" 
                                               wire:change="updateItem({{ $id }}, 'quantity', $event.target.value)"
                                               class="w-full text-center font-mono py-1 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" step="0.01" min="0" value="{{ $item['cost_price'] }}" 
                                               wire:change="updateItem({{ $id }}, 'cost_price', $event.target.value)"
                                               class="w-full text-right font-mono py-1 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-slate-800">
                                        ${{ number_format($item['subtotal'], 2) }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <button wire:click="removeItem({{ $id }})" class="text-slate-300 hover:text-rose-500">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Agrega productos desde el buscador de la derecha.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('items') <span class="text-rose-500 text-[10px] block mt-2">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- BUSCADOR DE PRODUCTOS Y RESUMEN (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Buscador de Productos -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Buscar y Agregar Productos</h2>
                <input wire:model.live.debounce.250ms="searchProduct" type="text" placeholder="Código o nombre..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500 mb-3">

                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @forelse($products as $product)
                        <button wire:click="addProduct({{ $product->id }})" 
                                class="w-full p-2.5 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 rounded-xl transition-all text-left flex justify-between items-center group">
                            <div>
                                <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">{{ $product->name }}</p>
                                <span class="text-[10px] text-slate-400">Stock Actual: {{ $product->stock }}</span>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-[18px]">add_circle</span>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Sin resultados.</p>
                    @endforelse
                </div>
            </div>

            <!-- Resumen y Procesamiento -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Resumen de Compra</h2>

                <div class="space-y-2 text-xs border-b border-slate-100 pb-3">
                    <div class="flex justify-between font-extrabold text-slate-800 text-base pt-1">
                        <span>TOTAL COMPRA:</span>
                        <span class="font-mono text-indigo-600">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Notas u Observaciones</label>
                    <textarea wire:model="notes" rows="2" placeholder="Opcional..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                </div>

                <button wire:click="savePurchase" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> REGISTRAR ENTRADA DE COMPRA
                </button>
            </div>
        </div>
    </div>
</div>