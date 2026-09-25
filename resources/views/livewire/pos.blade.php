<div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-6rem)]">
        
        <!-- SECCIÓN IZQUIERDA: CATÁLOGO Y PRODUCTOS (8 Columnas) -->
        <div class="lg:col-span-7 flex flex-col h-full bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-5">
            
            <!-- Encabezado y Buscador -->
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-between mb-4">
                <div class="relative w-full sm:w-72">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input wire:model.live.debounce.250ms="searchProduct" type="text" placeholder="Buscar producto o código..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Selector de Categoría -->
                <div class="w-full sm:w-auto">
                    <select wire:model.live="selectedCategory" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 py-2.5 px-3 focus:outline-none focus:border-indigo-500">
                        <option value="all">Todas las Categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grilla de Productos -->
            <div class="flex-1 overflow-y-auto pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                    @forelse($products as $product)
                        <button wire:click="addToCart({{ $product->id }})" 
                                class="flex flex-col justify-between p-3.5 bg-slate-50/70 hover:bg-indigo-50/50 border border-slate-200/80 hover:border-indigo-300 rounded-2xl transition-all group text-left relative overflow-hidden active:scale-[0.98]">
                            
                            <!-- Badges de Stock -->
                            <div class="flex items-center justify-between w-full mb-2">
                                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider truncate max-w-[80px]">
                                    {{ $product->code }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->stock <= $product->min_stock ? 'bg-amber-100 text-amber-700' : 'bg-slate-200/70 text-slate-600' }}">
                                    Stock: {{ $product->stock }}
                                </span>
                            </div>

                            <!-- Nombre del Producto -->
                            <h3 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors line-clamp-2 mb-2 leading-tight">
                                {{ $product->name }}
                            </h3>

                            <!-- Precio -->
                            <div class="flex items-baseline justify-between mt-auto pt-2 border-t border-slate-200/50 w-full">
                                <span class="text-xs font-medium text-slate-400">Precio</span>
                                <span class="text-base font-extrabold text-indigo-600 font-mono">
                                    ${{ number_format($product->sale_price, 2) }}
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-16 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">inventory_2</span>
                            <p class="text-sm">No se encontraron productos disponibles.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- SECCIÓN DERECHA: CARRITO Y RESUMEN DE COMPRA (5 Columnas) -->
        <div class="lg:col-span-5 flex flex-col h-full bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden p-5">
            
            <!-- Encabezado Carrito -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">shopping_cart</span>
                    <h2 class="text-base font-bold text-slate-800">Orden Actual</h2>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-rose-500 hover:text-rose-700 font-medium transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Vaciar
                    </button>
                @endif
            </div>

            <!-- Selección de Cliente -->
            <div class="mb-3 relative">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cliente</label>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-2">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] ml-1">person</span>
                    <div class="flex-1 truncate">
                        <span class="text-xs font-semibold text-slate-700 block truncate">
                            {{ $selectedCustomerName ?? 'Seleccionar Cliente' }}
                        </span>
                    </div>
                    <input wire:model.live.debounce.250ms="searchCustomer" type="text" placeholder="Cambiar cliente..." 
                           class="w-32 px-2 py-1 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Desplegable de búsqueda de clientes -->
                @if($searchCustomer)
                    <div class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-20 overflow-hidden max-h-40 overflow-y-auto">
                        @forelse($customers as $customer)
                            <button wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->name }}')" 
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-indigo-50 flex items-center justify-between border-b border-slate-100 last:border-0">
                                <span class="font-medium text-slate-800">{{ $customer->name }}</span>
                                <span class="font-mono text-slate-400 text-[10px]">{{ $customer->full_document }}</span>
                            </button>
                        @empty
                            <div class="p-2.5 text-xs text-slate-400 text-center">No se hallaron clientes</div>
                        @endforelse
                    </div>
                @endif
            </div>

            <!-- Lista de Productos en el Carrito -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 pr-1 my-1">
                @forelse($cart as $item)
                    <div class="py-2.5 flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $item['name'] }}</p>
                            <p class="text-[11px] font-mono text-slate-400">${{ number_format($item['price'], 2) }} c/u</p>
                        </div>

                        <!-- Controles de Cantidad -->
                        <div class="flex items-center gap-1.5 bg-slate-100 rounded-lg p-1">
                            <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" 
                                    class="w-5 h-5 flex items-center justify-center bg-white text-slate-700 hover:bg-slate-200 rounded text-xs font-bold shadow-2xs">
                                -
                            </button>
                            <span class="w-6 text-center text-xs font-bold text-slate-800 font-mono">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" 
                                    class="w-5 h-5 flex items-center justify-center bg-white text-slate-700 hover:bg-slate-200 rounded text-xs font-bold shadow-2xs">
                                +
                            </button>
                        </div>

                        <!-- Subtotal por ítem -->
                        <div class="text-right min-w-[60px]">
                            <span class="text-xs font-extrabold text-slate-800 font-mono block">
                                ${{ number_format($item['subtotal'], 2) }}
                            </span>
                        </div>

                        <!-- Botón Eliminar -->
                        <button wire:click="removeFromCart({{ $item['id'] }})" class="text-slate-300 hover:text-rose-500 transition-colors p-1">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-slate-300 py-10">
                        <span class="material-symbols-outlined text-5xl mb-2">add_shopping_cart</span>
                        <p class="text-xs font-medium text-slate-400">Carrito vacío. Selecciona productos.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desglose de Totales y Método de Pago -->
            <div class="border-t border-slate-100 pt-3 space-y-2 mt-auto">
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">${{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        Descuento ($):
                    </span>
                    <input wire:model.live="discount" type="number" step="0.01" min="0" 
                           class="w-20 text-right font-mono text-xs py-0.5 px-2 bg-slate-50 border border-slate-200 rounded-md focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex justify-between text-base font-extrabold text-slate-800 pt-2 border-t border-slate-100">
                    <span>TOTAL A PAGAR:</span>
                    <span class="font-mono text-xl text-indigo-600">${{ number_format($grandTotal, 2) }}</span>
                </div>

                <!-- Método de Pago -->
                <div class="pt-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Método de Pago</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button wire:click="$set('paymentMethod', 'cash')" 
                                class="py-1.5 text-[11px] font-semibold rounded-lg border transition-all {{ $paymentMethod === 'cash' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                            Efectivo
                        </button>
                        <button wire:click="$set('paymentMethod', 'mobile_pay')" 
                                class="py-1.5 text-[11px] font-semibold rounded-lg border transition-all {{ $paymentMethod === 'mobile_pay' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                            Pago Móvil
                        </button>
                        <button wire:click="$set('paymentMethod', 'card')" 
                                class="py-1.5 text-[11px] font-semibold rounded-lg border transition-all {{ $paymentMethod === 'card' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                            Punto / Tarjeta
                        </button>
                        <button wire:click="$set('paymentMethod', 'transfer')" 
                                class="py-1.5 text-[11px] font-semibold rounded-lg border transition-all {{ $paymentMethod === 'transfer' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                            Transferencia
                        </button>
                    </div>
                </div>

                <!-- Monto Pagado y Cambio (Si es efectivo) -->
                @if($paymentMethod === 'cash')
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Monto Entregado</label>
                            <input wire:model.live="paidAmount" type="number" step="0.01" min="0" placeholder="0.00" 
                                   class="w-full font-mono text-sm font-bold py-1 px-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Vuelto / Cambio</label>
                            <div class="w-full font-mono text-sm font-bold py-1 px-2.5 bg-slate-100 border border-slate-200 rounded-lg text-emerald-600">
                                ${{ number_format($changeAmount, 2) }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botón Completar Venta -->
                <button wire:click="processSale" 
                        wire:loading.attr="disabled"
                        class="w-full mt-3 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-emerald-200 flex items-center justify-center gap-2">
                    <span wire:loading wire:target="processSale" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    COMPLETAR VENTA (${{ number_format($grandTotal, 2) }})
                </button>
            </div>
        </div>
    </div>
</div>