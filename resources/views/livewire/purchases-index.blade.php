<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Historial de Compras</h1>
            <p class="text-xs text-slate-500">Facturas de entrada de mercancía registradas</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Nueva Compra / Entrada
        </a>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
        <div class="relative w-full sm:w-80">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Nº Factura o Proveedor..." 
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Nº Factura</th>
                        <th class="py-3.5 px-6">Fecha</th>
                        <th class="py-3.5 px-6">Proveedor</th>
                        <th class="py-3.5 px-6">Forma Pago</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-indigo-600">
                                {{ $purchase->invoice_number }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">
                                {{ $purchase->created_at->format('d/m/Y - h:i A') }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-800">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="py-3.5 px-6 capitalize text-slate-600">
                                {{ $purchase->payment_method }}
                            </td>
                            <td class="py-3.5 px-6 text-right font-mono font-bold text-slate-800">
                                ${{ number_format($purchase->total, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <button wire:click="showPurchaseDetails({{ $purchase->id }})" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">shopping_bag</span>
                                <p>No se registraron compras.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $purchases->links() }}
        </div>
    </div>

    <!-- MODAL DETALLE -->
    @if($showDetailModal && $selectedPurchase)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-base font-bold text-slate-800">Factura: {{ $selectedPurchase->invoice_number }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="text-xs space-y-2">
                    <p><strong>Proveedor:</strong> {{ $selectedPurchase->supplier->name }}</p>
                    <p><strong>Registrado por:</strong> {{ $selectedPurchase->user->name ?? 'Sistema' }}</p>
                    
                    <div class="py-2 border-t border-b border-slate-100 max-h-48 overflow-y-auto space-y-1">
                        @foreach($selectedPurchase->details as $detail)
                            <div class="flex justify-between">
                                <span>{{ $detail->product->name }} ({{ $detail->quantity }} x ${{ number_format($detail->cost_price, 2) }})</span>
                                <span class="font-mono font-bold">${{ number_format($detail->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between font-bold text-sm pt-2">
                        <span>Total:</span>
                        <span class="font-mono text-indigo-600">${{ number_format($selectedPurchase->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>