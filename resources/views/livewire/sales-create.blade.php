<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Emitir Factura de Venta / Pedido</h1>
            <p class="text-xs text-slate-500">Facturación formal a clientes registrados con descuento de inventario</p>
        </div>
        <a href="{{ route('sales') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
            Volver al Historial
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- DATOS PRINCIPALES Y DETALLE (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Datos del Cliente y Condiciones -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Información de la Venta</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Cliente *</label>
                        <select wire:model="customer_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="">Seleccionar Cliente...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->full_document }})</option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Condición de Pago</label>
                        <select wire:model.live="sale_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="cash">Contado</option>
                            <option value="credit">Crédito</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Método de Pago</label>
                        <select wire:model="payment_method" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            <option value="transfer">Transferencia</option>
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta / Punto</option>
                            <option value="mobile_pay">Pago Móvil</option>
                        </select>
                    </div>

                    @if($sale_type === 'credit')
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Vencimiento Crédito *</label>
                            <input wire:model="due_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500">
                            @error('due_date') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabla de Ítems a Facturar -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Productos en Factura</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                                <th class="py-2.5 px-3">Producto</th>
                                <th class="py-2.5 px-3 w-28 text-center">Cantidad</th>
                                <th class="py-2.5 px-3 w-32 text-right">Precio Unit. ($)</th>
                                <th class="py-2.5 px-3 w-32 text-right">Subtotal</th>
                                <th class="py-2.5 px-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($items as $id => $item)
                                <tr>
                                    <td class="py-3 px-3 font-bold text-slate-800">
                                        {{ $item['name'] }}
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $item['code'] }} | Stock: {{ $item['stock'] }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" min="1" max="{{ $item['stock'] }}" value="{{ $item['quantity'] }}" 
                                               wire:change="updateItem({{ $id }}, 'quantity', $event.target.value)"
                                               class="w-full text-center font-mono py-1 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" step="0.01" min="0" value="{{ $item['price'] }}" 
                                               wire:change="updateItem({{ $id }}, 'price', $event.target.value)"
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
                                        Selecciona productos desde el catálogo de la derecha.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('items') <span class="text-rose-500 text-[10px] block mt-2">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- BUSCADOR DE PRODUCTOS Y RESUMEN (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Buscador -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Catálogo de Productos</h2>
                <input wire:model.live.debounce.250ms="searchProduct" type="text" placeholder="Buscar por código o nombre..." 
                       class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500 mb-3">

                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @forelse($products as $product)
                        <button wire:click="addProduct({{ $product->id }})" 
                                class="w-full p-2.5 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 rounded-xl transition-all text-left flex justify-between items-center group">
                            <div>
                                <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">{{ $product->name }}</p>
                                <span class="text-[10px] text-slate-400">Stock: {{ $product->stock }} | ${{ number_format($product->sale_price, 2) }}</span>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-[18px]">add_circle</span>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No hay productos disponibles.</p>
                    @endforelse
                </div>
            </div>

            <!-- Resumen de Totales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cálculo de Factura</h2>

                <div class="space-y-2 text-xs border-b border-slate-100 pb-3">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal:</span>
                        <span class="font-mono font-semibold">${{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-500">
                        <span>Descuento ($):</span>
                        <input wire:model.live="discount" type="number" step="0.01" min="0" 
                               class="w-20 text-right font-mono text-xs py-0.5 px-2 bg-slate-50 border border-slate-200 rounded-md focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="flex justify-between font-extrabold text-slate-800 text-base pt-2 border-t border-slate-100">
                        <span>TOTAL FACTURA:</span>
                        <span class="font-mono text-indigo-600">${{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Observaciones</label>
                    <textarea wire:model="notes" rows="2" placeholder="Opcional..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                </div>

                <button wire:click="saveSale" 
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">description</span> EMITIR FACTURA DE VENTA
                </button>
            </div>
        </div>
    </div>
</div>