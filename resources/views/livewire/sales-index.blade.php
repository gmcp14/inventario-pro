<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Historial de Ventas</h1>
            <p class="text-xs text-slate-500">Consulta de comprobantes, facturas de venta y estado de transacciones</p>
        </div>
        <a href="{{ route('sales.create') }}"
            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add</span> Nueva Factura de Venta
        </a>

    </div>
    <!-- Tarjetas de Resumen / KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">payments</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventas de Hoy</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono">${{ number_format($todayTotal, 2) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventas Completadas</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono">{{ $completedCount }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">cancel</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventas Anuladas</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono">{{ $cancelledCount }}</p>
            </div>
        </div>
    </div>
    <div>


    </div>

    <!-- Filtros de Búsqueda -->
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Buscar Comprobante / Cliente</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Ej: V-20260924-0001..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Desde</label>
                <input wire:model.live="dateFrom" type="date"
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Hasta</label>
                <input wire:model.live="dateTo" type="date"
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1">Estado</label>
                <select wire:model.live="status"
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="all">Todos los Estados</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Anulada</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Comprobante</th>
                        <th class="py-3.5 px-6">Fecha / Hora</th>
                        <th class="py-3.5 px-6">Cliente</th>
                        <th class="py-3.5 px-6">Método Pago</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6 text-center">Estado</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-6 font-mono font-bold text-indigo-600">
                            {{ $sale->voucher_number }}
                        </td>
                        <td class="py-3.5 px-6 text-slate-500">
                            {{ $sale->created_at->format('d/m/Y H:i A') }}
                        </td>
                        <td class="py-3.5 px-6 font-semibold text-slate-800">
                            {{ $sale->customer->name }}
                        </td>
                        <td class="py-3.5 px-6 capitalize text-slate-600">
                            {{ str_replace('_', ' ', $sale->payment_method) }}
                        </td>
                        <td class="py-3.5 px-6 text-right font-mono font-bold text-slate-800">
                            ${{ number_format($sale->total, 2) }}
                        </td>
                        <td class="py-3.5 px-6 text-center">
                            @if($sale->status === 'completed')
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                Completada
                            </span>
                            @else
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                Anulada
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-center">
                            <button wire:click="showSaleDetails({{ $sale->id }})"
                                class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors"
                                title="Ver Detalle">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                            <a href="{{ route('reports.sale-invoice', $sale->id) }}" target="_blank"
                                class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">receipt_long</span>
                            <p>No se registraron ventas con los criterios seleccionados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- MODAL DETALLE DE VENTA -->
    @if($showDetailModal && $selectedSale)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-xl w-full p-6 overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Comprobante: {{ $selectedSale->voucher_number }}</h3>
                    <p class="text-xs text-slate-400">{{ $selectedSale->created_at->format('d/m/Y - h:i A') }}</p>
                </div>
                <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Info Cliente y Usuario -->
            <div class="grid grid-cols-2 gap-4 py-3 border-b border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block">Cliente:</span>
                    <span class="font-bold text-slate-700">{{ $selectedSale->customer->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Cajero:</span>
                    <span class="font-bold text-slate-700">{{ $selectedSale->user->name ?? 'Sistema' }}</span>
                </div>
            </div>

            <!-- Artículos -->
            <div class="py-3 max-h-48 overflow-y-auto divide-y divide-slate-100">
                @foreach($selectedSale->details as $detail)
                <div class="py-2 flex justify-between items-center text-xs">
                    <div>
                        <p class="font-bold text-slate-800">{{ $detail->product->name }}</p>
                        <p class="text-slate-400 font-mono">{{ $detail->quantity }} x
                            ${{ number_format($detail->price, 2) }}</p>
                    </div>
                    <span class="font-mono font-bold text-slate-800">${{ number_format($detail->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>

            <!-- Resumen de Pago -->
            <div class="pt-3 border-t border-slate-100 space-y-1 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-mono">${{ number_format($selectedSale->subtotal, 2) }}</span>
                </div>
                @if($selectedSale->discount > 0)
                <div class="flex justify-between text-slate-500">
                    <span>Descuento:</span>
                    <span class="font-mono">-${{ number_format($selectedSale->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between font-extrabold text-sm text-slate-800 pt-1 border-t border-slate-100">
                    <span>Total:</span>
                    <span class="font-mono text-indigo-600">${{ number_format($selectedSale->total, 2) }}</span>
                </div>
            </div>

            <!-- Acciones del Modal -->
            <div class="mt-6 flex justify-between items-center pt-3 border-t border-slate-100">
                @if($selectedSale->status === 'completed')
                <button wire:click="cancelSale({{ $selectedSale->id }})"
                    wire:confirm="¿Estás seguro de anular esta venta? El stock de los productos será restituido."
                    class="px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold rounded-xl transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">cancel</span> Anular Venta
                </button>
                @else
                <span class="text-xs font-bold text-rose-500">Venta Anulada</span>
                @endif

                <button wire:click="closeDetailModal"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>