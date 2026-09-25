<div>
    <!-- CDN de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Panel de Control / Dashboard</h1>
        <p class="text-xs text-slate-500">Métricas clave e indicadores de rendimiento del negocio</p>
    </div>

    <!-- TARJETAS KPIS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Venta Hoy -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ventas de Hoy</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono mt-1">${{ number_format($todaySales, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">payments</span>
            </div>
        </div>

        <!-- Ventas del Mes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ventas del Mes</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono mt-1">${{ number_format($monthSales, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">trending_up</span>
            </div>
        </div>

        <!-- Compras del Mes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Compras del Mes</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono mt-1">${{ number_format($monthPurchases, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">shopping_cart</span>
            </div>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Stock Bajo / Crítico</p>
                <p class="text-2xl font-extrabold text-slate-800 font-mono mt-1">{{ $lowStockCount }} <span class="text-xs text-slate-400 font-normal">prod.</span></p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICOS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        
        <!-- Gráfico de Ventas vs Compras (8 cols) -->
        <div class="lg:col-span-8 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Comparativo: Ventas vs Compras (Últimos 7 días)</h2>
            <div class="relative h-72">
                <canvas id="salesVSpurchasesChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Productos Más Vendidos (4 cols) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Top 5 Productos Más Vendidos</h2>
            <div class="relative h-72 flex items-center justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE TABLAS RESUMEN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Últimas Ventas (7 cols) -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Últimas Ventas Emitidas</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase">
                            <th class="py-2.5 px-3">Comprobante</th>
                            <th class="py-2.5 px-3">Cliente</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                            <th class="py-2.5 px-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($recentSales as $sale)
                            <tr>
                                <td class="py-3 px-3 font-mono font-bold text-indigo-600">{{ $sale->voucher_number }}</td>
                                <td class="py-3 px-3 text-slate-700">{{ $sale->customer->name ?? 'Cliente General' }}</td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-slate-800">${{ number_format($sale->total, 2) }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Completada</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400 text-xs">Sin registros recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alerta de Reorden de Inventario (5 cols) -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Productos por Reponer (Stock Bajo)</h2>
            <div class="space-y-2">
                @forelse($lowStockProducts as $product)
                    <div class="p-3 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $product->name }}</p>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $product->code }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg bg-rose-500 text-white font-mono font-extrabold text-xs">{{ $product->stock }} u.</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Mín: {{ $product->min_stock }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-emerald-600 text-xs font-bold flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Todo el inventario está en niveles óptimos.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SCRIPT CHART.JS -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            initCharts();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
        });

        function initCharts() {
            // 1. Gráfico de Ventas vs Compras
            const ctxSales = document.getElementById('salesVSpurchasesChart');
            if (ctxSales) {
                const existingChart = Chart.getChart(ctxSales);
                if (existingChart) existingChart.destroy();

                const last7Data = @json($last7Days);

                new Chart(ctxSales, {
                    type: 'bar',
                    data: {
                        labels: last7Data.map(item => item.day),
                        datasets: [
                            {
                                label: 'Ventas ($)',
                                data: last7Data.map(item => item.sales),
                                backgroundColor: '#4f46e5',
                                borderRadius: 8,
                            },
                            {
                                label: 'Compras ($)',
                                data: last7Data.map(item => item.purchases),
                                backgroundColor: '#f59e0b',
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // 2. Gráfico Donut de Productos Más Vendidos
            const ctxTop = document.getElementById('topProductsChart');
            if (ctxTop) {
                const existingChartTop = Chart.getChart(ctxTop);
                if (existingChartTop) existingChartTop.destroy();

                const topData = @json($topProducts);

                new Chart(ctxTop, {
                    type: 'doughnut',
                    data: {
                        labels: topData.map(item => item.name),
                        datasets: [{
                            data: topData.map(item => item.total_qty),
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        }
    </script>
</div>